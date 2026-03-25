<?php

namespace Tests\Unit\Domain\Recipe\Services;

use App\Domain\Recipe\DTOs\CreateRecipeDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeIngredientDTO;
use App\Domain\Recipe\DTOs\FieldsRecipeStepDTO;
use App\Domain\Recipe\Models\Recipe;
use App\Domain\Recipe\Repositories\RecipeRepository;
use App\Domain\Recipe\Services\RecipeService;
use App\Domain\Recipe\Services\RecipeServiceInterface;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\TestUnitCase;

class RecipeServiceTest extends TestUnitCase
{
    public function testCreateReturnsRecipe(): void
    {
        $this->mockTransaction();

        $step = $this->getFieldsRecipeStepDTO();
        $ingredient = $this->getFieldsRecipeIngredientDTO();
        $dto = $this->getCreateRecipeDTO([$step], [$ingredient]);
        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($dto)
            ->willReturn($recipe)
        ;

        $repository
            ->expects($this->once())
            ->method('createStep')
        ;

        $repository
            ->expects($this->once())
            ->method('createIngredient')
        ;

        $service = $this->getRecipeService($repository);
        $result = $service->create($dto);

        $this->assertSame($recipe, $result);
    }

    public function testCreateThrowsOnException(): void
    {
        $this->mockTransaction();

        $dto = $this->getCreateRecipeDTO();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getRecipeService($repository);
        $service->create($dto);
    }

    public function testDeleteCallsRepository(): void
    {
        $this->mockTransaction();

        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with($recipe)
        ;

        $service = $this->getRecipeService($repository);
        $service->delete($recipe);
    }

    public function testDeleteThrowsOnException(): void
    {
        $this->mockTransaction();

        $recipe = $this->getRecipe();
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('delete')
            ->willThrowException(new \RuntimeException('DB error'))
        ;

        $this->expectException(\RuntimeException::class);

        $service = $this->getRecipeService($repository);
        $service->delete($recipe);
    }

    public function testGetByUserReturnsCollection(): void
    {
        $user = $this->getUser();
        $recipe = $this->getRecipe();
        $collection = new Collection([$recipe]);
        $repository = $this->getRecipeRepository();

        $repository
            ->expects($this->once())
            ->method('findByFields')
            ->with(
                $this->isInstanceOf(FieldsRecipeDTO::class),
                ['steps', 'recipeIngredients.ingredient'],
            )
            ->willReturn($collection)
        ;

        $service = $this->getRecipeService($repository);
        $result = $service->getByUser($user);

        $this->assertSame($collection, $result);
    }

    private function getRecipeService(
        ?RecipeRepository $repository = null,
    ): RecipeServiceInterface {
        return new RecipeService(
            $repository ?? $this->createStub(RecipeRepository::class),
        );
    }

    private function getRecipeRepository(): RecipeRepository&MockObject
    {
        return $this->createMock(RecipeRepository::class);
    }

    /**
     * @param array<int, FieldsRecipeStepDTO> $steps
     * @param array<int, FieldsRecipeIngredientDTO> $ingredients
     */
    private function getCreateRecipeDTO(
        array $steps = [],
        array $ingredients = [],
    ): CreateRecipeDTO&MockObject {
        $mock = $this->createMock(CreateRecipeDTO::class);
        $mock->method('getSteps')->willReturn($steps);
        $mock->method('getIngredients')->willReturn($ingredients);

        return $mock;
    }

    private function getFieldsRecipeStepDTO(): FieldsRecipeStepDTO&MockObject
    {
        $mock = $this->createMock(FieldsRecipeStepDTO::class);
        $mock->method('getPosition')->willReturn(1);
        $mock->method('getDescription')->willReturn('Step 1');

        return $mock;
    }

    private function getFieldsRecipeIngredientDTO(): FieldsRecipeIngredientDTO&MockObject
    {
        $mock = $this->createMock(FieldsRecipeIngredientDTO::class);
        $mock->method('getPosition')->willReturn(1);
        $mock->method('getIngredientId')->willReturn('ingredient-uuid');
        $mock->method('getQuantity')->willReturn(1.5);

        return $mock;
    }

    private function getRecipe(
        string $id = 'recipe-uuid',
    ): Recipe&MockObject {
        $mock = $this->createConfiguredModelMock(Recipe::class, [
            'id' => $id,
        ]);

        $mock->method('load')->willReturnSelf();

        return $mock;
    }

    private function getUser(
        string $id = 'user-uuid',
    ): User&MockObject {
        return $this->createConfiguredModelMock(User::class, [
            'id' => $id,
        ]);
    }
}
