<?php

use yii\db\Migration;

/**
 * Handles the creation of table `book`.
 * Has foreign keys to the tables:
 *
 * - `user`
 */
class m170726_125416_create_book_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('book', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'user_id' => $this->integer()->notNull(),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-book-user_id',
            'book',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-book-user_id',
            'book',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `user`
        $this->dropForeignKey(
            'fk-book-user_id',
            'book'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-book-user_id',
            'book'
        );

        $this->dropTable('book');
    }
}
