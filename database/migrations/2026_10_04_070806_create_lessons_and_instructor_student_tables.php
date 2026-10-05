<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('instructor_student')) {
            Schema::create('instructor_student', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['instructor_id', 'student_id']);
            });
        }

        if (! Schema::hasTable('lessons')) {
            Schema::create('lessons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_id')->constrained()->cascadeOnDelete();
                $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('title')->nullable();
                $table->string('type')->default('conduite');
                $table->dateTime('starts_at');
                $table->dateTime('ends_at');
                $table->string('status')->default('planifie');
                $table->string('pickup_location')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('lessons', function (Blueprint $table) {
                if (! Schema::hasColumn('lessons', 'title')) {
                    $table->string('title')->nullable()->after('student_id');
                }
                if (! Schema::hasColumn('lessons', 'type')) {
                    $table->string('type')->default('conduite')->after('title');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_student');
    }
};
