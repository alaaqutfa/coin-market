<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->decimal('monthly_hours', 8, 2)->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->nullable()->unique();
                $table->string('phone')->nullable()->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('address')->nullable();
                $table->string('location')->nullable();
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->rememberToken();
                $table->timestamps();
            });
        } else {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->unique()->after('email');
                }
                if (! Schema::hasColumn('users', 'address')) {
                    $table->string('address')->nullable()->after('password');
                }
                if (! Schema::hasColumn('users', 'location')) {
                    $table->string('location')->nullable()->after('address');
                }
                if (! Schema::hasColumn('users', 'role_id')) {
                    $table->foreignId('role_id')->nullable()->after('location')->constrained('roles')->nullOnDelete();
                }
            });
        }

        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('employee_code')->unique();
                $table->decimal('salary', 10, 2)->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('email')->nullable()->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('brands')) {
            Schema::create('brands', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('logo')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('barcode')->unique();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->string('symbol', 5)->default('$');
                $table->integer('quantity')->default(0);
                $table->string('weight')->nullable();
                $table->string('image_path')->nullable();
                $table->json('social_media_urls')->nullable();
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
                $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_barcode_logs')) {
            Schema::create('product_barcode_logs', function (Blueprint $table) {
                $table->id();
                $table->string('barcode')->index();
                $table->boolean('exists')->default(false);
                $table->string('source')->nullable();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('attendance_logs')) {
            Schema::create('attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('date')->index();
                $table->timestamp('check_in')->nullable();
                $table->string('check_in_photo')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->string('check_out_photo')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('work_schedules')) {
            Schema::create('work_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week');
                $table->boolean('is_alternate')->default(false);
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->decimal('work_hours', 5, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('daily_work_hours')) {
            Schema::create('daily_work_hours', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->date('date');
                $table->decimal('required_hours', 5, 2)->default(0);
                $table->decimal('actual_hours', 5, 2)->default(0);
                $table->timestamps();
                $table->unique(['employee_id', 'date']);
            });
        }

        if (! Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        //
    }
};
