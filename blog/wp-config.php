<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa user o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/pt-br:Editando_wp-config.php
 *
 * @package WordPress
 */
 
 define('WP_SITEURL', 'http://barramais.com.br/blog');

// ** Configurações do MySQL - Você pode pegar estas informações
// com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define('DB_NAME', 'barrablog');

/** Usuário do banco de dados MySQL */
define('DB_USER', 'barrablog');

/** Senha do banco de dados MySQL */
define('DB_PASSWORD', 'vorteX20**');

/** Nome do host do MySQL */
define('DB_HOST', 'barrablog.mysql.dbaas.com.br');

/** Charset do banco de dados a ser usado na criação das tabelas. */
define('DB_CHARSET', 'utf8mb4');

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define('DB_COLLATE', '');
define('FS_METHOD','direct');

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para desvalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Mt;X^;Vlu;sl9!k^1iOI:;Bh@-N0>Km1Z[pfk-=wec*15680n[@sr.j2I@BKLT`F');
define('SECURE_AUTH_KEY',  '@rj:lC_^eZJC0Mtn(,<((Oq}lxH.A>8r<>9[hEFy@Vu!K]M[Q8`UJ>5dj5b`DkXw');
define('LOGGED_IN_KEY',    '/G~B`J]@)fR,&iB1Q>O)SKA|jZrq(9kXlH33O0tBX*WVQfCQ<<4iPhxO}Rw+EM%H');
define('NONCE_KEY',        '@Hz}te!i`WuK[&8gy(U~kAnf%K;XX![YG&W+ Ig]ciB6V#L`&[}$[1}b,iGvmMEr');
define('AUTH_SALT',        '.pP 9<]da(Xax&#$JK`R-t_BJ52t0iX]%BI9E&1T7s]N]E/#4]$b79e.WbL/%P.u');
define('SECURE_AUTH_SALT', '*nY/m}nue$+~~`pt7+J?6XtFN?),L&%vYqKLK!p:vxtc*+-Y1a`@$7sNe}mvIC#4');
define('LOGGED_IN_SALT',   'Q/put/}FMyxu,5b7e6>|N>k2  a7Rhd)Ma/9HbZEQU^|M+c;be<u2i_|8>2c&11}');
define('NONCE_SALT',       '$klLjXMX=;,~)*NfL_6{8^JH)v}>W/`pehTPqH+x A<^7b{;mLT].ngntjf,+5ad');

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * para cada um um único prefixo. Somente números, letras e sublinhados!
 */
$table_prefix  = 'wp_';

/**
 * Para desenvolvedores: Modo debugging WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://codex.wordpress.org/pt-br:Depura%C3%A7%C3%A3o_no_WordPress
 */
define('WP_DEBUG', true);

/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Configura as variáveis e arquivos do WordPress. */
require_once(ABSPATH . 'wp-settings.php');
