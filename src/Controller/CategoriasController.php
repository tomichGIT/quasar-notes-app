<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;       // para obtener datos de la petición (GET, POST, PUT, DELETE, etc.)
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Categorias;
use App\Repository\CategoriasRepository;

class CategoriasController extends AbstractController
{
    #[Route('/categorias', name: 'app_categorias')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to the Categorias Controller!',
            'path' => 'src/Controller/CategoriasController.php',
        ]);
    }

    #[Route('/list_categorias', name: 'list_categorias')]
    public function listCategorias(Request $request, CategoriasRepository $categoriasRepository)
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
        return $response;

    }



    #[Route('/create_categoria', name: 'create_categoria', methods: ['POST'])]
    public function createCategoria(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $A_data = json_decode($request->getContent(), true);
        $txt_categoria=$A_data["txt_categoria"];

        if(empty($txt_categoria)){
            $response = new JsonResponse();
            $response->setData(['success' => false, 'data' => [], 'msg' => 'Nombre de categoría inválido']);
            return $response;
        }

        $now=new \DateTime();

        $categoria = new Categorias();
        $categoria->setCategoria($txt_categoria);
        $categoria->setCreatedAt($now);

        $em->persist($categoria); // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $em->flush(); // actually executes the queries (i.e. the INSERT query)

        $response = new JsonResponse(); // convierte un array en JSON
        $response->setData([
            'success' => true,
            'data' => ["id" => $categoria->getId(), "categoria" => $categoria->getCategoria(), "fecha" => $categoria->getCreatedAt()->format('d-m-Y H:i')],
            "msg" => "Categoría creada con éxito"
        ]);
        return $response;
    }

    #[Route('/delete_categoria/{idCategoria}', name: 'delete_categoria', methods: ['POST'])]
    public function deleteCategoria(int $idCategoria, CategoriasRepository $categoriasRepository, EntityManagerInterface $em)
    {
 
        if (!$idCategoria) {  return new JsonResponse([ 'success' => false, 'msg' => 'No se ha proporcionado un ID de categoría' ]); }
        $categoria = $categoriasRepository->find($idCategoria);
        if (!$categoria) {    return new JsonResponse([ 'success' => false, 'msg' => "No se encontró la categoría con el ID $idCategoria"]); }
    
        $txt_categoria = $categoria->getCategoria(); // use the getter method to retrieve the name

        $em->remove($categoria); // si fueran varios resultados, utilizo $em->remove($usuario) dentro de un foreach
        $em->flush();
        
        return new JsonResponse([
            'success' => true,
            'data' => $txt_categoria,
            'msg' => "Categoría $txt_categoria eliminada con éxito",
        ]);

    }
}
