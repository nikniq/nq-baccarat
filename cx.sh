#!/bin/bash
#================================================================
# NQ Baccarat — LOCAL command-line assistant (cx.sh)
#
#   ./cx.sh            # interactive menu
#   ./cx.sh 20         # run an option directly
#   ./cx.sh 91 "note"  # …with a second parameter
#
# Runs on your LOCAL machine only — never copy this to the server.
# Its counterpart on the server is deploy.sh.
#
# Requires a vars.sh next to it (see vars.sh.example).
#================================================================
clear

if [ ! -f ./vars.sh ]; then
  echo "vars.sh not found next to cx.sh — copy vars.sh.example to vars.sh and fill it in."
  exit 1
fi
source ./vars.sh

REMOTE_APP="${REMOTE_APP:-www/$PROJECT}"
APP_URL="${APP_URL:-https://$PROJECT}"
LOG_OUTPUT="${LOG_OUTPUT:-./log/remote.out}"

if [ "$(hostname -s 2>/dev/null)" = "${REMOTE_HOST%%.*}" ]; then
  echo "⚠ This looks like $REMOTE_HOST — cx.sh is the LOCAL script. Use deploy.sh here."
  exit 1
fi

#---------------------------------------------------------------
# Helpers
#---------------------------------------------------------------

log_history() {
  mkdir -p ./log
  touch ./log/history.log
  printf '%s  %s\n' "$(date '+%Y-%m-%d %H:%M')" "$1" > tmpfile
  cat ./log/history.log >> tmpfile && mv tmpfile ./log/history.log
}

remote() {
  $APP_SSH "$REMOTE" "cd ~/$REMOTE_APP && $*"
}

remote_tty() {
  $APP_SSH -t "$REMOTE" "cd ~/$REMOTE_APP && $*"
}

need_param() {
  if [ -z "$PARAM2" ]; then
    read -r -p "$1: " PARAM2
  fi
}

banner() {
  echo ===========================================================
  echo "$1"
  echo ===========================================================
}

confirm() {
  echo "⚠ $1"
  read -r -p "Type 'yes' to continue: " reply
  [ "$reply" = "yes" ] || { echo "Skipped $SELECTION."; exit 0; }
}

echo =============================================================
echo "Hi $USER@$HOSTNAME — acting on $PROJECT ($REMOTE)"
echo What do you want to do?
echo -------------------------------------------------------------
echo "ROUTINE"
echo "20 : DEPLOY: pull + migrate + caches on $PROJECT"
echo "21 : DEPLOY + re-run one seeder class"
echo "22 : WHAT'S LIVE: compare server HEAD with local"
echo "23 : MIGRATIONS: status (pending on the server)"
echo "24 : CACHES: refresh config/routes/views"
echo "25 : CACHES: clear everything"
echo "26 : COMPOSER: install production dependencies"
echo ----------------------------------------------
echo DIAGNOSE
echo "30 : HEALTH: $APP_URL"
echo "31 : LOGS: tail the Laravel log"
echo "32 : LOGS: last errors with stack traces"
echo "33 : ARTISAN: run a command"
echo "34 : CONFIG: key settings on the server"
echo "35 : DISK: what is using the account quota"
echo "36 : LOGS: file sizes"
echo ----------------------------------------------
echo "QUEUE & SCHEDULE"
echo "50 : QUEUE: list failed jobs"
echo "51 : QUEUE: retry all failed jobs"
echo "52 : QUEUE: drain the queue now"
echo "53 : SCHEDULE: what is registered"
echo ----------------------------------------------
echo MAINTENANCE
echo "70 : MAINTENANCE MODE: on  [site goes down]"
echo "71 : MAINTENANCE MODE: off"
echo "72 : LOGS: truncate the Laravel log"
echo ----------------------------------------------
echo ACCESS
echo "00 : CONNECT: to $REMOTE"
echo "01 : RSYNC: ssh settings and dotfiles to remote"
echo "02 : RSYNC: copy rm.sh to remote"
echo "03 : SETUP BACKUP: copy bk.sh to remote"
echo "05 : MONITOR: remote top"
echo "06 : TERMINATE ACCESS: delete remote ssh directory"
echo ----------------------------------------------
echo OTHER
echo "91 : BACKUP: remote database and files"
echo "99 : LOCAL FILE UPDATE and SECURITY SCRUB"
echo "qq : Exit [Quit]"
echo Enter [Selection] to continue
echo =============================================================
if [ -n "$1" ]; then
  SELECTION=$1
else
  read  -n 2 SELECTION
fi
PARAM2="${2:-}"
echo
echo Your selection is : $SELECTION.


case "$SELECTION" in

  #-------------------------------------------------------------
  # ROUTINE
  #-------------------------------------------------------------

  "20" )
  banner "DEPLOY: $PROJECT"
  if [ -n "$(git status --porcelain 2>/dev/null)" ]; then
    echo "⚠ Uncommitted local changes — these will NOT deploy:"
    git status --short
    echo
  fi
  UNPUSHED=$(git log --oneline @{u}.. 2>/dev/null)
  if [ -n "$UNPUSHED" ]; then
    echo "⚠ Commits not pushed — these will NOT deploy:"
    echo "$UNPUSHED"
    echo
    read -r -p "Deploy anyway? Type 'yes' to continue: " confirm
    [ "$confirm" = "yes" ] || { echo "Skipped $SELECTION"; exit 0; }
  fi
  echo "Local HEAD : $(git log --oneline -1)"
  echo
  remote_tty "./deploy.sh"
  log_history "Deployed $PROJECT"
  ;;


  "21" )
  banner "DEPLOY + SEEDER: $PROJECT"
  need_param "Seeder class (e.g. GameSeeder)"
  remote_tty "./deploy.sh $PARAM2"
  log_history "Deployed $PROJECT with seeder $PARAM2"
  ;;


  "22" )
  banner "WHAT'S LIVE on $PROJECT"
  echo "--- server ---"
  remote "git log --oneline -3 && git status --short"
  echo
  echo "--- local ---"
  git log --oneline -3
  echo
  LOCAL_HEAD=$(git rev-parse HEAD 2>/dev/null)
  SERVER_HEAD=$(remote "git rev-parse HEAD" 2>/dev/null | tr -d '\r')
  if [ "$LOCAL_HEAD" = "$SERVER_HEAD" ]; then
    echo "✓ server matches local HEAD"
  else
    echo "✗ server is NOT on your local HEAD"
    echo "  local : $LOCAL_HEAD"
    echo "  server: $SERVER_HEAD"
  fi
  ;;


  "23" )
  banner "MIGRATIONS: status on $PROJECT"
  remote "php artisan migrate:status"
  ;;


  "24" )
  banner "CACHES: refresh on $PROJECT"
  remote "php artisan optimize"
  log_history "Refreshed caches on $PROJECT"
  ;;


  "25" )
  banner "CACHES: clear everything on $PROJECT"
  remote "php artisan optimize:clear"
  log_history "Cleared all caches on $PROJECT"
  ;;


  "26" )
  banner "COMPOSER: install production dependencies"
  remote_tty "composer install --no-dev --optimize-autoloader --no-interaction"
  log_history "Installed composer dependencies on $PROJECT"
  ;;


  #-------------------------------------------------------------
  # DIAGNOSE
  #-------------------------------------------------------------

  "30" )
  banner "HEALTH: $APP_URL"
  curl -sS -o /dev/null -w 'status   : %{http_code}\ntotal    : %{time_total}s\nttfb     : %{time_starttransfer}s\nredirect : %{redirect_url}\n' \
    --max-time 25 "$APP_URL" || echo "✗ $APP_URL did not respond"
  echo
  echo "--- /up (health check) ---"
  curl -sS -w '\nstatus   : %{http_code}\n' --max-time 25 "$APP_URL/up" || echo "✗ no response"
  ;;


  "31" )
  banner "LOGS: tail on $PROJECT (Ctrl-C to stop)"
  remote_tty "tail -n 40 -f storage/logs/laravel.log"
  ;;


  "32" )
  banner "LOGS: recent errors on $PROJECT"
  remote "grep -nE '\.(ERROR|CRITICAL|ALERT|EMERGENCY):' storage/logs/laravel.log | tail -n 25" \
    || echo "No matching lines (or no log file yet)."
  ;;


  "33" )
  banner "ARTISAN on $PROJECT"
  need_param "artisan command (without the word 'artisan')"
  remote_tty "php artisan $PARAM2"
  log_history "Ran artisan $PARAM2 on $PROJECT"
  ;;


  "34" )
  banner "CONFIG on $PROJECT"
  echo "--- environment ---"
  remote "grep -E '^(APP_ENV|APP_DEBUG|APP_URL|SESSION_DRIVER|CACHE_STORE|QUEUE_CONNECTION|MAIL_MAILER|DB_CONNECTION)=' .env | sort"
  echo
  echo "--- keys present (values hidden) ---"
  remote "grep -oE '^[A-Z_]*(KEY|SECRET|TOKEN|PASSWORD)[A-Z_]*=' .env | tr -d '=' | sort | sed 's/^/  /'"
  ;;


  "35" )
  banner "DISK: quota and the biggest directories"
  remote "du -sh . 2>/dev/null; echo; echo 'largest directories:'; du -sh */ 2>/dev/null | sort -rh | head -12"
  ;;


  "36" )
  banner "LOGS: sizes on $PROJECT"
  remote "ls -lh storage/logs/ 2>/dev/null | tail -n +2; echo; echo 'total:'; du -sh storage/logs 2>/dev/null"
  ;;


  #-------------------------------------------------------------
  # QUEUE & SCHEDULE
  #-------------------------------------------------------------

  "50" )
  banner "QUEUE: failed jobs"
  remote "php artisan queue:failed"
  ;;


  "51" )
  banner "QUEUE: retry all failed jobs"
  remote_tty "php artisan queue:retry all"
  log_history "Retried failed jobs on $PROJECT"
  ;;


  "52" )
  banner "QUEUE: drain now"
  remote_tty "php artisan queue:work database --stop-when-empty --max-time=120"
  ;;


  "53" )
  banner "SCHEDULE on $PROJECT"
  remote "php artisan schedule:list"
  ;;


  #-------------------------------------------------------------
  # MAINTENANCE
  #-------------------------------------------------------------

  "70" )
  banner "MAINTENANCE MODE: ON"
  confirm "$APP_URL will start returning 503 to everyone until you run 71."
  remote_tty "php artisan down --retry=60"
  log_history "Maintenance mode ON for $PROJECT"
  ;;


  "71" )
  banner "MAINTENANCE MODE: OFF"
  remote_tty "php artisan up"
  log_history "Maintenance mode OFF for $PROJECT"
  ;;


  "72" )
  banner "LOGS: truncate the Laravel log"
  confirm "This discards the Laravel log on $PROJECT."
  remote "test -f storage/logs/laravel.log && : > storage/logs/laravel.log && echo 'laravel.log truncated.' || echo 'No laravel.log to truncate.'"
  log_history "Truncated the Laravel log on $PROJECT"
  ;;


  #-------------------------------------------------------------
  # ACCESS
  #-------------------------------------------------------------

  "00" )
  banner "CONNECT: to $REMOTE"
  $APP_SSH $REMOTE
  log_history "Connect to remote"
  ;;


  "01" )
  banner "GAIN ACCESS: Copy ssh settings from local to remote"
  $APP_SSH $REMOTE 'if [ -d ~/.ssh ]; then echo remote SSH directory exists; else echo Creating remote SSH directory; mkdir ~/.ssh/; fi'
  $APP_SCP ~/.ssh/remote/$PROJECT/shell/authorized_keys  $REMOTE:~/.ssh/authorized_keys
  $APP_SCP ~/.ssh/remote/$PROJECT/shell/known_hosts  $REMOTE:~/.ssh/known_hosts
  $APP_SSH $REMOTE 'chmod 700 ~/.ssh; [ -e ~/.ssh/authorized_keys ] && chmod 600 ~/.ssh/authorized_keys'

  $APP_SCP ~/.ssh/remote/$PROJECT/shell/.vimrc $REMOTE:~/.vimrc
  $APP_SCP ~/.ssh/remote/$PROJECT/shell/.alias $REMOTE:~/.alias
  $APP_SCP ~/.ssh/remote/$PROJECT/shell/.bashrc $REMOTE:~/.bashrc
  $APP_SCP ~/.ssh/remote/$PROJECT/shell/.bash_logout $REMOTE:~/.bash_logout
  $APP_SSH $REMOTE 'echo "source ~/.alias" >> ~/.bash_profile'
  log_history "GAIN ACCESS: Copy ssh settings from local to remote"
  ;;


  "02" )
  banner "GAIN CONTROL: Copy rm.sh to remote"
  $APP_SSH $REMOTE "mkdir -p ~/$PROJECT/source/"
  $APP_SCP ~/.ssh/remote/$PROJECT/rm.sh  $REMOTE:~/.ssh/rm.sh
  $APP_SCP ~/.ssh/remote/$PROJECT/vars.sh  $REMOTE:~/.ssh/vars.sh
  $APP_SSH $REMOTE 'chmod 754 ~/.ssh/rm.sh'
  log_history "GAIN CONTROL: Copy rm.sh to remote"
  ;;


  "03" )
  banner "BACKUP CAPABILITY: Copy bk.sh to remote"
  $APP_SSH $REMOTE "mkdir -p ~/$REMOTE_APP/backup && chmod 700 ~/$REMOTE_APP/backup"
  $APP_SCP ~/.ssh/remote/$PROJECT/bk.sh   $REMOTE:~/$REMOTE_APP/bk.sh
  $APP_SCP ~/.ssh/remote/$PROJECT/vars.sh $REMOTE:~/$REMOTE_APP/vars.sh
  $APP_SSH $REMOTE "chmod 755 ~/$REMOTE_APP/bk.sh"
  log_history "BACKUP CAPABILITY: Copy bk.sh to remote"
  ;;


  "05" )
  banner "MONITOR: Remote Top"
  $APP_SSH -t $REMOTE 'top'
  ;;


  "06" )
  banner "TERMINATE ACCESS: Delete remote ssh directory"
  confirm "This wipes ~/.ssh on $REMOTE — you may lock yourself out."
  $APP_SSH $REMOTE 'cd ~/.ssh/ && rm -f *'
  log_history "Terminate remote ssh settings"
  ;;


  #-------------------------------------------------------------
  # OTHER
  #-------------------------------------------------------------

  "91" )
  banner "BACKUP: database and files"
  need_param "Backup description"
  $APP_SSH -t "$REMOTE" "~/$REMOTE_APP/bk.sh 01 '$PARAM2'"
  $APP_SSH -t "$REMOTE" "~/$REMOTE_APP/bk.sh 02 '$PARAM2'"
  log_history "Backup database and files: $PARAM2"
  ;;


  "99" )
  banner "LOCAL FILE UPDATE and SECURITY SCRUB"
  START_PWD="$PWD"
  for d in shell log files; do
    [ -d "$d" ] || mkdir "$d"
  done
  touch ./log/history.log
  cp $LOCAL_SCRIPTS_DEFAULT/README.txt .
  cp $LOCAL_SCRIPTS_DEFAULT/shell/.alias ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/shell/.vimrc ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/shell/.bashrc ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/shell/.bash_logout ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/shell/authorized_keys ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/shell/known_hosts ./shell
  cp $LOCAL_SCRIPTS_DEFAULT/rm.sh .
  cp $LOCAL_SCRIPTS_DEFAULT/bk.sh .

  for stray in .alias .vimrc .bashrc .bash_logout authorized_keys known_hosts; do
    [ -f "./$stray" ] && rm -f "./$stray"
  done

  chmod 700 ~/.ssh
  for k in ~/.ssh/id*; do
    [ -f "$k" ] || continue
    case "$k" in
      *.pub) chmod 644 "$k" ;;
      *)     chmod 600 "$k" ;;
    esac
  done
  [ -e ~/.ssh/authorized_keys ] && chmod 600 ~/.ssh/authorized_keys
  cd "$START_PWD"
  log_history "Refreshed files from Default"
  ;;


  "\x1B" | "^[" )
  echo "ESC"
  exit 0
  ;;


  "qq" )
  echo Quit
  exit 0
  ;;


  * )
  echo
  echo "Not a recognized option."
  ;;

esac
