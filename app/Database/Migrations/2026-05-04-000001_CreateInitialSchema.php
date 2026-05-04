<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovieSchema extends Migration
{
    public function up()
    {
        // MOVIE
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'tmdb_id' => ['type' => 'INT', 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'LONGTEXT', 'null' => true],
            'year' => ['type' => 'INT', 'null' => true],
            'pic' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'rating' => ['type' => 'TINYINT', 'null' => true],
            'length' => ['type' => 'INT', 'null' => true],
            'language' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tmdb_id');
        $this->forge->createTable('movie');

        // GENRES
        $this->forge->addField([
            'id' => ['type' => 'INT'],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'description' => ['type' => 'LONGTEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('genres');

        // PEOPLE
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true],
            'tmdb_id' => ['type' => 'INT', 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'surname' => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => ''],
            'pic' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'bio' => ['type' => 'LONGTEXT', 'null' => true],
            'sex' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tmdb_id');
        $this->forge->createTable('people');

        // MOVIE_GENRES
        $this->forge->addField([
            'genres_id' => ['type' => 'INT'],
            'movie_id' => ['type' => 'INT'],
        ]);
        $this->forge->addKey(['genres_id', 'movie_id'], true);
        $this->forge->addForeignKey('genres_id', 'genres', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('movie_id', 'movie', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('movie_genres');

        // MOVIE_PEOPLE
        $this->forge->addField([
            'people_id' => ['type' => 'INT'],
            'movie_id' => ['type' => 'INT'],
            'role' => ['type' => 'VARCHAR', 'constraint' => 50],
        ]);
        $this->forge->addKey(['people_id', 'movie_id'], true);
        $this->forge->addForeignKey('people_id', 'people', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('movie_id', 'movie', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('movie_people');
    }

    public function down()
    {
        $this->forge->dropTable('movie_people');
        $this->forge->dropTable('movie_genres');
        $this->forge->dropTable('people');
        $this->forge->dropTable('genres');
        $this->forge->dropTable('movie');
    }
}