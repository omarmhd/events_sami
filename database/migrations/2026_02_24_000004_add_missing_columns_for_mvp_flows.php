<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsForMvpFlows extends Migration
{
    public function up()
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (!Schema::hasColumn('employees', 'position')) {
                    $table->string('position')->nullable()->after('email');
                }

                if (!Schema::hasColumn('employees', 'nationality')) {
                    $table->string('nationality')->nullable()->after('position');
                }

                if (!Schema::hasColumn('employees', 'event_name')) {
                    $table->string('event_name')->nullable()->after('employee_number');
                }
            });
        }

        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                if (!Schema::hasColumn('tickets', 'employee_email')) {
                    $table->string('employee_email')->nullable()->after('employee_name');
                }

                if (!Schema::hasColumn('tickets', 'position')) {
                    $table->string('position')->nullable()->after('employee_number');
                }

                if (!Schema::hasColumn('tickets', 'nationality')) {
                    $table->string('nationality')->nullable()->after('position');
                }

                if (!Schema::hasColumn('tickets', 'type')) {
                    $table->string('type', 32)->default('employee')->after('nationality');
                }

                if (!Schema::hasColumn('tickets', 'date')) {
                    $table->date('date')->nullable()->after('description');
                }

                if (!Schema::hasColumn('tickets', 'from_time')) {
                    $table->time('from_time')->nullable()->after('date');
                }

                if (!Schema::hasColumn('tickets', 'to_time')) {
                    $table->time('to_time')->nullable()->after('from_time');
                }

                if (!Schema::hasColumn('tickets', 'checked_in_at')) {
                    $table->timestamp('checked_in_at')->nullable()->after('barcode');
                }
            });
        }

        if (Schema::hasTable('event_invitations')) {
            Schema::table('event_invitations', function (Blueprint $table) {
                if (!Schema::hasColumn('event_invitations', 'flow_type')) {
                    $table->enum('flow_type', ['private', 'public'])->default('private')->after('company_id');
                }

                if (!Schema::hasColumn('event_invitations', 'source')) {
                    $table->enum('source', ['manual', 'csv_import', 'resend'])->default('manual')->after('flow_type');
                }

                if (!Schema::hasColumn('event_invitations', 'last_sent_at')) {
                    $table->timestamp('last_sent_at')->nullable()->after('responded_at');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                $columns = ['position', 'nationality', 'event_name'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('employees', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('tickets')) {
            Schema::table('tickets', function (Blueprint $table) {
                $columns = [
                    'employee_email',
                    'position',
                    'nationality',
                    'type',
                    'date',
                    'from_time',
                    'to_time',
                    'checked_in_at',
                ];

                foreach ($columns as $column) {
                    if (Schema::hasColumn('tickets', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        if (Schema::hasTable('event_invitations')) {
            Schema::table('event_invitations', function (Blueprint $table) {
                $columns = ['flow_type', 'source', 'last_sent_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('event_invitations', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
}
