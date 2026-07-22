<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('industry_business_types', function (Blueprint $table) {
            $table->id();
            $table->string('industry')->unique();
            $table->timestamps();
        });

        Schema::create('sap_products', function (Blueprint $table) {
            $table->id();
            $table->string('sap_product')->unique();
            $table->timestamps();
        });

        $now = now();

        foreach ($this->industries() as $industry) {
            DB::table('industry_business_types')->insertOrIgnore([
                'industry' => $industry,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($this->sapProducts() as $product) {
            DB::table('sap_products')->insertOrIgnore([
                'sap_product' => $product,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        Schema::table('clients', function (Blueprint $table) {
            $table->foreignId('industry_business_type_id')->nullable()->after('building_details')->constrained('industry_business_types')->restrictOnDelete();
            $table->foreignId('sap_product_id')->nullable()->after('industry_business_type_id')->constrained('sap_products')->restrictOnDelete();
            $table->string('version_number', 40)->nullable()->after('sap_product_id');
            $table->string('patch_or_fp', 120)->nullable()->after('version_number');
        });

        $industryNames = DB::table('industry_business_types')->pluck('id', 'industry');
        $productNames = DB::table('sap_products')->pluck('id', 'sap_product');
        $industryLabels = $this->industryLabels();
        $productLabels = $this->sapProductLabels();

        DB::table('clients')
            ->select(['id', 'industry_type', 'sap_product_used', 'software_version_patch'])
            ->orderBy('id')
            ->each(function (object $client) use ($industryLabels, $industryNames, $now, $productLabels, $productNames): void {
                $industry = $industryLabels[$client->industry_type] ?? $this->labelFromLegacyValue((string) $client->industry_type);
                $product = $productLabels[$client->sap_product_used] ?? $this->labelFromLegacyValue((string) $client->sap_product_used);

                if (! $industryNames->has($industry)) {
                    $id = DB::table('industry_business_types')->insertGetId([
                        'industry' => $industry,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $industryNames->put($industry, $id);
                }

                if (! $productNames->has($product)) {
                    $id = DB::table('sap_products')->insertGetId([
                        'sap_product' => $product,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $productNames->put($product, $id);
                }

                DB::table('clients')
                    ->where('id', $client->id)
                    ->update([
                        'industry_business_type_id' => $industryNames[$industry],
                        'sap_product_id' => $productNames[$product],
                        'version_number' => $client->software_version_patch,
                        'patch_or_fp' => null,
                    ]);
            });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['industry_type', 'status']);
            $table->dropColumn(['industry_type', 'sap_product_used', 'software_version_patch']);
            $table->index(['industry_business_type_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['industry_business_type_id', 'status']);
            $table->string('industry_type')->nullable()->after('building_details');
            $table->string('sap_product_used')->nullable()->after('industry_type');
            $table->string('software_version_patch', 120)->nullable()->after('sap_product_used');
        });

        DB::table('clients')
            ->leftJoin('industry_business_types', 'clients.industry_business_type_id', '=', 'industry_business_types.id')
            ->leftJoin('sap_products', 'clients.sap_product_id', '=', 'sap_products.id')
            ->select([
                'clients.id',
                'clients.version_number',
                'clients.patch_or_fp',
                'industry_business_types.industry',
                'sap_products.sap_product',
            ])
            ->orderBy('clients.id')
            ->each(function (object $client): void {
                DB::table('clients')
                    ->where('id', $client->id)
                    ->update([
                        'industry_type' => Str::slug((string) $client->industry),
                        'sap_product_used' => Str::slug((string) $client->sap_product),
                        'software_version_patch' => trim((string) $client->version_number.' '.(string) $client->patch_or_fp),
                    ]);
            });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropConstrainedForeignId('industry_business_type_id');
            $table->dropConstrainedForeignId('sap_product_id');
            $table->dropColumn(['version_number', 'patch_or_fp']);
            $table->index(['industry_type', 'status']);
        });

        Schema::dropIfExists('sap_products');
        Schema::dropIfExists('industry_business_types');
    }

    /**
     * @return array<int, string>
     */
    private function industries(): array
    {
        return array_values($this->industryLabels());
    }

    /**
     * @return array<string, string>
     */
    private function industryLabels(): array
    {
        return [
            'manufacturing' => 'Manufacturing',
            'distribution-retail' => 'Distribution / Retail',
            'services' => 'Services',
            'healthcare' => 'Healthcare',
            'construction' => 'Construction',
            'food-beverage' => 'Food & Beverage',
            'others' => 'Others',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function sapProducts(): array
    {
        return array_values($this->sapProductLabels());
    }

    /**
     * @return array<string, string>
     */
    private function sapProductLabels(): array
    {
        return [
            'sap-business-one' => 'SAP Business One',
            'sap-s4hana' => 'SAP S/4HANA',
            'sap-btp' => 'SAP Business Technology Platform',
            'sap-successfactors' => 'SAP SuccessFactors',
            'sap-businessobjects' => 'SAP BusinessObjects',
            'other' => 'Other SAP Product',
        ];
    }

    private function labelFromLegacyValue(string $value): string
    {
        $label = Str::of($value)->replace('-', ' ')->title()->trim()->toString();

        return $label !== '' ? $label : 'Others';
    }
};
