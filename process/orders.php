<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if($method === "GET") {

        $pedidosQuery = $conn->query("SELECT * FROM pedidos");

        $pedidos = $pedidosQuery->fetchAll();

        $pizzas = [];
    
    
        //Montando pizzas//
        foreach($pedidos as $pedido) {

            $pizza = [];

            // definir um array para a pizza//
            $pizza['id'] = $pedido['pizza_id'];

            //resgatado a pizza//
            $pizzaQuery = $conn->prepare("SELECT * FROM pizzas WHERE id = :pizza_id");

            $pizzaQuery->bindParam(":pizza_id", $pizza['id']);

            $pizzaQuery->execute();

            $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);
            
            // Resgatando a borda da pizza//
            $bordaQuery = $conn->prepare("SELECT * FROM bordas WHERE id = :borda_id");

            $bordaQuery->bindParam(":borda_id", $pizzaData['borda_id']);

            $bordaQuery->execute();

            $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);
            
            $pizza['borda'] = $borda['tipo'];


              // Resgatando a borda da massa//
            $massaQuery = $conn->prepare("SELECT * FROM massas WHERE id = :massa_id");

            $massaQuery->bindParam(":massa_id", $pizzaData['massa_id']);

            $massaQuery->execute();

            $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);
            
            $pizza['massa'] = $massa['tipo'];


            // Resgatando os sabores da pizza//
            $saboresQuery = $conn->prepare("SELECT * FROM pizza_sabor WHERE pizza_id = :pizza_id");

            $saboresQuery->bindParam(":pizza_id", $pizza['id']);

            $saboresQuery->execute();

            $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);

        
            //Resgatando o nome dos sabores//
            $SaboresDaPizza = [];

            $saborQuery = $conn->prepare("SELECT * FROM sabores WHERE id = :sabor_id");

            foreach($sabores as $sabor) {
               
                $saborQuery->bindParam(":sabor_id", $sabor['sabor_id']);
                
                $saborQuery->execute();
                
                $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);
                array_push($SaboresDaPizza, $saborPizza['nome']);
            }

            $pizza['sabores'] = $SaboresDaPizza;

            //Adicionando o status do pedido//

            $pizza['status'] = $pedido['status_id'];

            //adicionar o array de pizza no array de pizzas//

            array_push($pizzas, $pizza);
         
        }
            // Resgatando os status//
        $statusQuery = $conn->query("SELECT * FROM status");
        $status = $statusQuery->fetchAll();


    } else if($method === "POST") {

        //Verificar o tipo de ação//
        $type = $_POST['type'];

        //Deletar pedido//
        if($type === "delete") {
            
            $pizzaID= $_POST['id'];

            $deleteQuery = $conn->prepare("DELETE FROM pedidos WHERE pizza_id = :pizza_id");
            
            $deleteQuery->bindParam(":pizza_id", $pizzaID, PDO::PARAM_INT);
            
            $deleteQuery->execute();

            $_SESSION['msg'] = "Pedido deletado com sucesso!";
            $_SESSION['status'] = "success";

        }  else if($type === "update") {

            $pizzaID= $_POST['id'];
            $statusID = $_POST['status'];

            $updateQuery = $conn->prepare("UPDATE pedidos SET status_id = :status_id WHERE pizza_id = :pizza_id");

            $updateQuery->bindParam(":status_id", $statusID, PDO::PARAM_INT);
            $updateQuery->bindParam(":pizza_id", $pizzaID, PDO::PARAM_INT);

            $updateQuery->execute();

            $_SESSION['msg'] = "Status do pedido atualizado com sucesso!";
            $_SESSION['status'] = "success";

        }


        //Retornar para o dashboard//
        header("Location: ../dashboard.php");

    }

?>
    
    
