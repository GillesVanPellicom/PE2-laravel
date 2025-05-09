<?php
    namespace Database\Factories;
    use Illuminate\Database\Eloquent\Factories\Factory;
    use App\Models\Invoice;
    use App\Models\User;




    class InvoiceFactory extends Factory
    {
        protected $model = Invoice::class;

        public function definition()
        {
            return [
                'company_id' => User::where('isCompany', 1)->inRandomOrder()->first()?->id ?? User::factory(),
                'discount' => $this->faker->randomFloat(2, 0, 20),
                'expiry_date' => $this->faker->dateTimeBetween('+1 week', '+2 months'),
                'is_paid' => false,
                'paid_at' => function (array $attrs) {
                    return $attrs['is_paid'] ? $this->faker->dateTimeBetween('-1 months', 'now') : null;
                },
                'reference' => 'INV-'.$this->faker->unique()->numerify('#######'),
            ];
        }
    }
