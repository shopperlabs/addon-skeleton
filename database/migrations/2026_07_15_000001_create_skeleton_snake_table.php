<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shopper\Core\Helpers\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(shopper_table('skeleton_snake'), function (Blueprint $table): void {
            $this->addCommonFields($table);

            // add fields
        });
    }
};
