<?php

declare(strict_types=1);

use yii\db\Migration;

class m200518_121727_create_logger_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $query = <<<SQL
create table {{%logger}}
(
id bigint unsigned not null auto_increment,
user_id bigint unsigned,
created_at datetime,
action varchar(64) not null,
model_name varchar(64) not null,
model_id bigint unsigned not null,
attributes text,
description text,
primary key (id)
)
ENGINE = InnoDB;

create index log_user on {{%log}} (user_id);
create index log_model on {{%log}} (model_name);
create index log_id on {{%log}} (model_id);
create index log_action on {{%log}} (action);
SQL;
        $this->execute($query);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('log_user', '{{%log}}');
        $this->dropIndex('log_model', '{{%log}}');
        $this->dropIndex('log_id', '{{%log}}');
        $this->dropIndex('log_action', '{{%log}}');
        $this->dropTable('{{%log}}');
    }
}


