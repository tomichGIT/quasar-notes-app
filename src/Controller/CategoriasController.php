<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CategoriasController extends AbstractController
{
    #[Route('/categorias', name: 'app_categorias')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CategoriasController.php',
        ]);
    }

    #[Route('/list_categorias', name: 'list_categorias')]
    public function listCategorias(Request $request, UsuariosRepository $categoriasRepository)
    {
        $categorias = $categoriasRepository->findAll();
        $A_categorias = [];
        foreach ($categorias as $categoria){
            $A_categorias[] = [
                'id' => $categoria->getId(),
                'categoria' => $categoria->getCategoria(),
                'createdAt' => $categoria->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        $response = new JsonResponse(); // convierte un array en JSON
        $response->setData([
            'success' => true,
            'data' => $A_categorias,
            'msg' => "Categorías obtenidas con éxito",
            'cant' => count($A_categorias)
        ]);
    }
}
