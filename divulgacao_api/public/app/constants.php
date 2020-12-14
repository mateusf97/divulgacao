<?php

  /**
  * This file represents the constants used in this API
  * @example  define(constant, array)
  */

  define('PASSWORD_MIN_LENGTH', 7);

  /**
  * @static ERRORS represents errors messages used in API
  * @uses in class Error
  * @example  define(name, value)
  */

  define('ERRORS', array(
        'NOT_AUTHENTICATED'                             => 'Erro de autenticação. Verifique os dados e tente novamente',
        'UNDEFINED_ERROR'                               => 'Ocorreu um erro no sistema, tente novamente',
        'UNAUTHORIZED'                                  => 'Você não tem as permissões necessárias para executar esta ação',
        'PASSWORD_CHANGED_SUCCESSFULLY'                 => 'Senha alterada com sucesso',
        'CPF_ALREADY_EXISTS'                            => 'CPF já existe',
        'INVALID_CPF'                                   => 'CPF Inválido',
        'INVALID_LOGIN'                                 => 'Login Inválido',
        'INVALID_PASSWORD'                              => 'Senha inválida',
        'MISSING_CPF'                                   => 'Onde está o "cpf"?',
        'MISSING_LOGIN'                                 => 'Onde está o Login?',
        'MISSING_PASSWORD'                              => 'Onde está a "senha"?',


    )
  );

?>
