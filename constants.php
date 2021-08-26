<?php

/*Database*/
function getServernameForDBConnection() {
    return "localhost";
}
function getUsernameForDBConnection() {
    return "u788476771_vivek";
    //return "root";
}
function getPasswordForDBConnection() {
    return "Vivek@123";
    //return "";
}
function getDBNameForDBConnection() {
    return "u788476771_tyreosdb";
    //return "tyreosdb";
}

/*SMTP*/
function getSMTPHost() {
    return "smtp.hostinger.com";
}
function getSMTPPort() {
    return 465;
}
function getSMTPUserName(){
    //return "info@tyreoshield.com";
    return "admin@tyreoshield.com";
}
function getSMTPPassword(){
    //return "Tusharbhai@123";
    return "Admin@123";
}

/*To Tyreoshield Admin*/
function getTyreOShieldInfoMailId() {
    return "info.tyreoshield@gmail.com";
}

?>