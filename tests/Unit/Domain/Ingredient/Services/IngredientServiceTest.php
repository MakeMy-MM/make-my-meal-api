<?php

namespace Tests\Unit\Domain\Ingredient\Services;

use App\Domain\Ingredient\DTOs\CreateIngredientDTO;
use App\Domain\Ingredient\DTOs\UpdateIngredientDTO;
use App\Domain\Ingredient\Models\Ingredient;
use App\Domain\Ingredient\Repositories\IngredientRepository;
use App\Domain\Ingredient\Services\IngredientService;
use App\Domain\Ingredient\Services\IngredientServiceInterface;
use App\Domain\User\Models\User;
use App\Http\Exceptions\InternalServerErrorHttpException;
use Illuminate\Database\Eloquent\Collection;
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
        $ingredient = $this->getIngredient();
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

    public function testUpdateReturnsIngredient(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('commit')->once()->andReturnNull();

        $dto = $this->getUpdateIngredientDTO();
        $ingredient = $this->getIngredient();
        $repository = $this->getIngredientRepository();

        $repository
            ->expects($this->once())
            ->method('update')
            ->with($dto)
            ->willReturn($ingredient)
        ;

        $service = $this->getIngredientService($repository);
        $result = $service->update($dto);

        $this->assertSame($ingredient, $result);
    }

    public function testUpdateThrowsInternalServerErrorHttpException(): void
    {
        DB::shouldReceive('beginTransaction')->once()->andReturnNull();
        DB::shouldReceive('rollBack')->once()->andReturnNull();

        $dto = $this->getUpdateIngredientDTO();
        $repository = $this->getIngredientRepository();

        $repository
            ->expects($this->once())
            ->method('update')
            ->willThrowException(new InternalServerErrorHttpException())
        ;

        $this->expectException(InternalServerErrorHttpException::class);

        $service = $this->getIngredientService($repository);
        $service->update($dto);
    }

    public function testGetByUserReturnsCollection(): void
    {
        $user = $this->getUser();
        $ingredient = $this->getIngredient();
        $collection = new Collection([$ingredient]);
        $repository = $this->getIngredientRepository();

        $repository
            ->expects($this->once())
            ->method('findByFields')
            ->willReturn($collection)
        ;

        $service = $this->getIngredientService($repository);
        $result = $service->getByUser($user);

        $this->assertSame($collection, $result);
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

    private function getUpdateIngredientDTO(): UpdateIngredientDTO&MockObject
    {
        return $this->createMock(UpdateIngredientDTO::class);
    }

    private function getUser(
        string $id = 'user-uuid',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
        ]);
    }

    private function getIngredient(
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
