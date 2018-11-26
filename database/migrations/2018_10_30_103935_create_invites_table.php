<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel_invitation.invite_table_name'), function (Blueprint $table) {
            $table->char('id', 26)
                ->comment('[PK] 資料識別碼');

            $table->string('code')
                ->comment('邀請碼');

            $table->enum('status', ['enabled', 'disabled'])
                ->default('enabled')
                ->comment('邀請碼的開放狀態');

            $table->string('for')
                ->nullable()
                ->comment('邀請碼的專屬使用者 (null 表示所有人都可以使用)');

            $table->string('belong_to')
                ->nullable()
                ->comment('邀請碼的擁有者');

            $table->string('made_by')
                ->nullable()
                ->comment('邀請碼的製作者');

            $table->integer('max')
                ->nullable()
                ->comment('邀請碼的最大使用次數');

            $table->integer('uses')
                ->default(0)
                ->comment('邀請碼的已使用次數');

            $table->string('type')
                ->nullable()
                ->comment('邀請碼的類型');

            $table->timestamp('valid_until')
                ->nullable()
                ->comment('邀請碼的有效期限');

            $table->timestamps();

            // === 索引 ===
            // 指定主鍵索引
            $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('laravel_invitation.invite_table_name'));
    }
}
