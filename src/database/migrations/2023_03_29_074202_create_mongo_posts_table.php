<?php

use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMongoPostsTable extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->table('posts', function (Blueprint $collection) {
                $collection->index('_id');
                $collection->string('post_id');
                $collection->string('title');
                $collection->string('image');
                $collection->tinyInteger('readTime');
                $collection->string('permalink');
                $collection->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)
            ->table('posts', function (Blueprint $collection) {
                $collection->drop();
            });
    }
}
