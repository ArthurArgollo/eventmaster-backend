<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegistroTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function usuario_consegue_se_cadastrar_com_sucesso()
    {
        $response = $this->post('/cadastrar', [
            'name'     => 'Arthur Teste',
            'email'    => 'arthur@teste.com',
            'cpf'      => '123.456.789-00',
            'password' => 'senha123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'arthur@teste.com'
        ]);

        $response->assertSessionHas('sucesso');
    }

    /** @test */
    public function email_duplicado_retorna_erro_adequado()
    {
        User::factory()->create(['email' => 'duplicado@teste.com']);

        $response = $this->post('/cadastrar', [
            'name'     => 'Outro Arthur',
            'email'    => 'duplicado@teste.com',
            'cpf'      => '000.000.000-00',
            'password' => 'senha123',
        ]);
        $response->assertSessionHasErrors('email');
    }
}