<?php

namespace Tests\Unit\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Ingredient\Repositories\IngredientRepository;
use App\Domain\Ingredient\Services\IngredientService;
use App\Domain\Ingredient\Services\IngredientServiceInterface;
use App\Http\Exceptions\InternalServerErrorHttpException;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class IngredientServiceTest extends TestUnitCase
{
    public function testCreateReturnsIngredient(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $dto = $this->getCreateIngredientDTO();
        $ingredient = $this->getIngredientMock();
        $repository = $this->getIngredientRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($ingredient)
        ;

        $service = $this->getIngredientService($repository);
        $result = $service->create($dto);

        $this->assertSame($ingredient, $result);
    }

    public function testCreateThrowsInternalServerErrorHttpException(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $dto = $this->getCreateIngredientDTO();
        $repository = $this->getIngredientRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new InternalServerErrorHttpException())
        ;

        $this->expectException(InternalServerErrorHttpException::class);

        $service = $this->getIngredientService($repository);
        $service->create($dto);
    }

    private function getIngredientService(
        ?IngredientRepository $repository = null,
    ): IngredientServiceInterface {
        return new IngredientService(
            $repository ?? $this->createStub(IngredientRepository::class),
        );
    }

    private function getIngredientRepository(): IngredientRepository&MockObject
    {
        return $this->createMock(IngredientRepository::class);
    }

    private function getCreateIngredientDTO(): CreateIngredientDTO&MockObject
    {
        return $this->createMock(CreateIngredientDTO::class);
    }

    private function getIngredientMock(
        string $id = 'fake-uuid',
        string $name = 'Tomate',
        string $measurementUnit = 'kg',
    ): Ingredient&MockObject {
        return $this->createConfiguredModelMock(Ingredient::class, [
            'id' => $id,
            'name' => $name,
            'measurement_unit' => $measurementUnit,
        ]);
    }
}
