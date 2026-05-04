<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMovieSchema extends Migration
{
    public function up()
    {
        // MOVIE
        $this->forge->addField([
            'id' => ['type'=>'INT','auto_increment'=>true],
            'tmdb_id' => ['type'=>'INT','null'=>true],
            'name' => ['type'=>'VARCHAR','constraint'=>255],
            'description' => ['type'=>'TEXT','null'=>true],
            'release_date' => ['type'=>'DATE','null'=>true],
            'runtime' => ['type'=>'INT','null'=>true],
            'rating' => ['type'=>'DECIMAL','constraint'=>'3,1','null'=>true],
            'vote_count' => ['type'=>'INT','null'=>true],
            'revenue' => ['type'=>'BIGINT','null'=>true],
            'budget' => ['type'=>'BIGINT','null'=>true],
            'status' => ['type'=>'VARCHAR','constraint'=>50,'null'=>true],
            'adult' => ['type'=>'BOOLEAN','null'=>true],
            'original_language' => ['type'=>'VARCHAR','constraint'=>10],
            'original_title' => ['type'=>'VARCHAR','constraint'=>255],
            'popularity' => ['type'=>'FLOAT','null'=>true],
            'pic' => ['type'=>'VARCHAR','constraint'=>255,'null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tmdb_id');
        $this->forge->createTable('movie');

        // GENRES
        $this->forge->addField([
            'id'=>['type'=>'INT'],
            'name'=>['type'=>'VARCHAR','constraint'=>255],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('genres');

        // PEOPLE
        $this->forge->addField([
            'id'=>['type'=>'INT','auto_increment'=>true],
            'tmdb_id'=>['type'=>'INT','null'=>true],
            'name'=>['type'=>'VARCHAR','constraint'=>255],
            'gender'=>['type'=>'TINYINT','null'=>true],
            'birthday'=>['type'=>'DATE','null'=>true],
            'deathday'=>['type'=>'DATE','null'=>true],
            'place_of_birth'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],
            'popularity'=>['type'=>'FLOAT','null'=>true],
            'known_for_department'=>['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'profile_path'=>['type'=>'VARCHAR','constraint'=>255,'null'=>true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('tmdb_id');
        $this->forge->createTable('people');

        // RELATIONS
        $this->forge->addField([
            'genres_id'=>['type'=>'INT'],
            'movie_id'=>['type'=>'INT'],
        ]);
        $this->forge->addKey(['genres_id','movie_id'], true);
        $this->forge->addForeignKey('genres_id','genres','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('movie_id','movie','id','CASCADE','CASCADE');
        $this->forge->createTable('movie_genres');

        $this->forge->addField([
            'people_id'=>['type'=>'INT'],
            'movie_id'=>['type'=>'INT'],
            'role'=>['type'=>'VARCHAR','constraint'=>50],
        ]);
        $this->forge->addKey(['people_id','movie_id'], true);
        $this->forge->addForeignKey('people_id','people','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('movie_id','movie','id','CASCADE','CASCADE');
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