<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `book`
 */
class m170726_125745_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(),
            'text' => $this->text(),
            'user_id' => $this->integer()->notNull(),
            'book_id' => $this->integer(),
            'is_private' => $this->integer(1)->defaultValue(0),
        ]);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-article-user_id',
            'article',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-article-user_id',
            'article',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `book_id`
        $this->createIndex(
            'idx-article-book_id',
            'article',
            'book_id'
        );

        // add foreign key for table `book`
        $this->addForeignKey(
            'fk-article-book_id',
            'article',
            'book_id',
            'book',
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
            'fk-article-user_id',
            'article'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-article-user_id',
            'article'
        );

        // drops foreign key for table `book`
        $this->dropForeignKey(
            'fk-article-book_id',
            'article'
        );

        // drops index for column `book_id`
        $this->dropIndex(
            'idx-article-book_id',
            'article'
        );

        $this->dropTable('article');
    }
}
