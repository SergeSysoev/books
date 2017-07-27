<?php

use yii\db\Migration;

/**
 * Handles the creation of table `access_to_article`.
 * Has foreign keys to the tables:
 *
 * - `user`
 * - `article`
 */
class m170726_130017_create_access_to_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('access_to_article', [
            'user_id' => $this->integer()->notNull(),
            'article_id' => $this->integer()->notNull(),
        ]);

        $this->addPrimaryKey('user-article_pk', 'access_to_article', ['user_id', 'article_id']);

        // creates index for column `user_id`
        $this->createIndex(
            'idx-access_to_article-user_id',
            'access_to_article',
            'user_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-access_to_article-user_id',
            'access_to_article',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        // creates index for column `article_id`
        $this->createIndex(
            'idx-access_to_article-article_id',
            'access_to_article',
            'article_id'
        );

        // add foreign key for table `article`
        $this->addForeignKey(
            'fk-access_to_article-article_id',
            'access_to_article',
            'article_id',
            'article',
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
            'fk-access_to_article-user_id',
            'access_to_article'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            'idx-access_to_article-user_id',
            'access_to_article'
        );

        // drops foreign key for table `article`
        $this->dropForeignKey(
            'fk-access_to_article-article_id',
            'access_to_article'
        );

        // drops index for column `article_id`
        $this->dropIndex(
            'idx-access_to_article-article_id',
            'access_to_article'
        );

        $this->dropTable('access_to_article');
    }
}
