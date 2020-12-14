<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks {

  function _update() {
    return 'sudo apt-get update -y';
  }

  function _installNginx() {
    return 'sudo apt-get install nginx -y';
  }

  function _installPHP() {
    return 'sudo apt-get install php7.2 php7.2-dev php7.2-json php7.2-pspell \
            php7.2-xml php7.2-bcmath php7.2-enchant php7.2-ldap php7.2-readline \
            php7.2-xmlrpc php7.2-bz2 php7.2-fpm php7.2-mbstring php7.2-recode \
            php7.2-xsl php7.2-cgi php7.2-gd php7.2-mysql php7.2-snmp php7.2-zip \
            php7.2-cli php7.2-gmp php7.2-odbc php7.2-soap php7.2-common \
            php7.2-imap php7.2-opcache php7.2-sqlite3 php7.2-curl \
            php7.2-interbase php7.2-pgsql php7.2-sybase php7.2-dba php7.2-intl \
            php7.2-phpdbg php7.2-tidy -y';
  }


  /*
   * Install dependecies in ubuntu 18.04
   *  PHP7.2
   *  NGINX
   *
   * */

  function setup_machine() {
    $this->taskSshExec($this->HOST, $this->USER_HOST)
         ->identityFile($this->KEY_PEM)
         ->exec($this->_update())
         ->exec($this->_installNginx())
         ->exec($this->_installPHP())
         ->run();
  }
}
