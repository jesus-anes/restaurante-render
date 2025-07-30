<?php

namespace App\Controller\Api;

use App\Entity\Restaurante;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Restaurantes")
 */
#[Route('/api/restaurantes', name: 'api_restaurantes_')]
class RestauranteController extends AbstractController
{
    /**
     * Lista todos los restaurantes.
     *
     * @OA\Get(
     *     path="/api/restaurantes",
     *     summary="Obtener todos los restaurantes",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de restaurantes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nombre", type="string"),
     *                 @OA\Property(property="direccion", type="string"),
     *                 @OA\Property(property="telefono", type="string")
     *             )
     *         )
     *     )
     * )
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $restaurantes = $em->getRepository(Restaurante::class)->findAll();

        $data = array_map(fn($r) => [
            'id' => $r->getId(),
            'nombre' => $r->getNombre(),
            'direccion' => $r->getDireccion(),
            'telefono' => $r->getTelefono(),
        ], $restaurantes);

        return $this->json($data);
    }

    /**
     * Crea un nuevo restaurante.
     *
     * @OA\Post(
     *     path="/api/restaurantes",
     *     summary="Crear restaurante",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nombre", "direccion", "telefono"},
     *             @OA\Property(property="nombre", type="string", example="Bar Paco"),
     *             @OA\Property(property="direccion", type="string", example="Calle Mayor 1"),
     *             @OA\Property(property="telefono", type="string", example="123456789")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Restaurante creado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="id", type="integer")
     *         )
     *     )
     * )
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $restaurante = new Restaurante();
        $restaurante->setNombre($data['nombre'] ?? '');
        $restaurante->setDireccion($data['direccion'] ?? '');
        $restaurante->setTelefono($data['telefono'] ?? '');

        $em->persist($restaurante);
        $em->flush();

        return $this->json([
            'message' => 'Restaurante creado',
            'id' => $restaurante->getId()
        ], Response::HTTP_CREATED);
    }

    /**
     * Muestra un restaurante por ID.
     *
     * @OA\Get(
     *     path="/api/restaurantes/{id}",
     *     summary="Ver un restaurante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurante encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="direccion", type="string"),
     *             @OA\Property(property="telefono", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Restaurante no encontrado"
     *     )
     * )
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $em): JsonResponse
    {
        $restaurante = $em->getRepository(Restaurante::class)->find($id);

        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id' => $restaurante->getId(),
            'nombre' => $restaurante->getNombre(),
            'direccion' => $restaurante->getDireccion(),
            'telefono' => $restaurante->getTelefono()
        ]);
    }

    /**
     * Actualiza un restaurante existente.
     *
     * @OA\Put(
     *     path="/api/restaurantes/{id}",
     *     summary="Actualizar restaurante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="nombre", type="string"),
     *             @OA\Property(property="direccion", type="string"),
     *             @OA\Property(property="telefono", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurante actualizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Restaurante no encontrado"
     *     )
     * )
     */
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $restaurante = $em->getRepository(Restaurante::class)->find($id);

        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $restaurante->setNombre($data['nombre'] ?? $restaurante->getNombre());
        $restaurante->setDireccion($data['direccion'] ?? $restaurante->getDireccion());
        $restaurante->setTelefono($data['telefono'] ?? $restaurante->getTelefono());

        $em->flush();

        return $this->json(['message' => 'Restaurante actualizado']);
    }

    /**
     * Elimina un restaurante.
     *
     * @OA\Delete(
     *     path="/api/restaurantes/{id}",
     *     summary="Eliminar restaurante",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Restaurante eliminado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Restaurante no encontrado"
     *     )
     * )
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): JsonResponse
    {
        $restaurante = $em->getRepository(Restaurante::class)->find($id);

        if (!$restaurante) {
            return $this->json(['error' => 'Restaurante no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($restaurante);
        $em->flush();

        return $this->json(['message' => 'Restaurante eliminado']);
    }
}
