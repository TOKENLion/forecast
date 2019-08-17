<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%Countries}}`.
 */
class m190809_184524_Create_Countries_Table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%countries}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%countries}}');
    }
}
