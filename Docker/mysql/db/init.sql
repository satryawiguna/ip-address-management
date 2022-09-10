CREATE DATABASE IF NOT EXISTS `ip_address_management` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL ON `ip_address_management`.* TO 'root'@'%' IDENTIFIED BY 'mysql';

CREATE DATABASE IF NOT EXISTS `ip_address_management_test` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL ON `ip_address_management_test`.* TO 'root'@'%' IDENTIFIED BY 'mysql';
