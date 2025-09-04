<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    //Resgate dos dados, montagem do pedido//
    if($method === "GET") {
   
        $bordasQuery = $conn->QUERY("SELECT * FROM bordas;");
       
        $bordas = $bordasQuery->fetchAll();

        $massasQuery = $conn->QUERY("SELECT * FROM massas;");

        $massas = $massasQuery->fetchAll();

        $saboresQuery = $conn->QUERY("SELECT * FROM sabores;");
       
        $sabores = $saboresQuery->fetchAll();

   


    //Criação do pedido//

    }else if($method === "POST") {
   
        $data = $_POST;

        $borda = $data["borda"];
        $massa = $data["massa"];
        $sabores = $data["sabores"];

    // Validação de sabores máximos//
        if(count($sabores) > 3) {
            
            $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
            $_SESSION["status"] = "warning";

        }   else {

    // salvando borda e massa na pizza
        $stmt = $conn->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");
    
    // Filtrando inputs//
            $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
            $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

            $stmt->execute();
          
        // Resgatando o id da pizza//
            $pizzaid = $conn->lastInsertId();

            $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");
            
    //  Repetição até inserir todos os sabores//
            foreach($sabores as $sabor) {

        //Filtrando os inputs//
                $stmt->bindParam(":pizza", $pizzaid, PDO::PARAM_INT);
                $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);
                
                $stmt->execute();
            }  
       

        //criar o pedido da pizza//
        $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");
        
        // status -> sempre inicia com 1, que é em preparo//
        $statusid = 1;
       //filtrar os inputs//
       $stmt->bindParam(":pizza", $pizzaid);
       $stmt->bindParam(":status", $statusid);

         $stmt->execute();

         //Mensagem de sucesso//
         $_SESSION["msg"] = "Pedido realizado com sucesso!";    
            $_SESSION["status"] = "success";

            
    }

        
     //Retorna para página inicial//
     header("Location: ..");


    }
    ?>