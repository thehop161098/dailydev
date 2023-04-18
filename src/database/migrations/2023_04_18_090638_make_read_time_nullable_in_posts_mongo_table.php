<?php

use Illuminate\Database\Migrations\Migration;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeReadTimeNullableInPostsMongoTable extends Migration
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
            $collection->integer('readTime')->nullable()->change();
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
            $collection->integer('readTime')->nullable(false)->change();
        });
    }
}
