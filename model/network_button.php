<?php
namespace PodloveSubscribeButton\Model;

class NetworkButton extends Button {}

NetworkButton::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
NetworkButton::property( 'name', 'VARCHAR(255)' );
NetworkButton::property( 'title', 'VARCHAR(255)' );
NetworkButton::property( 'subtitle', 'VARCHAR(255)' );
NetworkButton::property( 'description', 'TEXT' );
NetworkButton::property( 'cover', 'VARCHAR(255)' );
NetworkButton::property( 'feeds', 'TEXT' );