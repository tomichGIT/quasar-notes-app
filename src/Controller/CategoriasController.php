<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;       // para obtener datos de la petición (GET, POST, PUT, DELETE, etc.)
use Symfony\Component\HttpFoundation\Response;
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

        return new JsonResponse([
            'success' => true,
            'data' => $A_categorias,
            'msg' => "Categorías obtenidas con éxito",
            'cant' => count($A_categorias)
        ]);
    }



    #[Route('/save_categoria/{idCategoria?}', name: 'save_categoria', methods: ['POST','PUT'])]
    public function saveCategoria(Request $request, EntityManagerInterface $em, int $idCategoria = null): JsonResponse
    {
        $A_data = json_decode($request->getContent(), true);
        $txt_categoria=$A_data["txt_categoria"];

        if(empty($txt_categoria)){
            return new JsonResponse(['success' => false, 'data' => [], 'msg' => 'Nombre de categoría inválido']);
        }

        $now=new \DateTime();

        // Create or Update
        // $categoria = $idCategoria ? $em->getRepository(Categorias::class)->find($idCategoria) : new Categorias();
        if($idCategoria){  
            $categoria = $em->getRepository(Categorias::class)->find($idCategoria); // update
            $msg="Categoria actualizada con éxito";
            $statusCode=Response::HTTP_OK; // 200
        } else {
            $categoria = new Categorias();  // insert
            $categoria->setCreatedAt($now);
            $msg="Categoría creada con éxito";
            $statusCode=Response::HTTP_CREATED; // 201
        }
        $categoria->setCategoria($txt_categoria);

        $em->persist($categoria); // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $em->flush(); // actually executes the queries (i.e. the INSERT query)

        return new JsonResponse([
            'success' => true,
            'data' => ["id" => $categoria->getId(), "categoria" => $categoria->getCategoria(), "fecha" => $categoria->getCreatedAt()->format('d-m-Y H:i')],
            "msg" => $msg,
            "status" => $statusCode
        ]);
    }


    #[Route('/delete_categoria/{idCategoria}', name: 'delete_categoria', methods: ['DELETE'])]
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
            "status" => Response::HTTP_NO_CONTENT
        ]);

    }
}
