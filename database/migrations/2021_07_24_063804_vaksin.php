<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Vaksin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaksin', function (Blueprint $table) {
            $table->bigIncrements('id_vaksin');
            $table->date('tgl_vaksin');
            $table->unsignedBigInteger('id_user');
            $table->text('lokasi');
            $table->string('foto');
            $table->enum('status', ['terkirim', 'proses', 'selesai', 'tolak']);
            $table->unsignedBigInteger('id_kategori');
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vaksin');
    }
}
