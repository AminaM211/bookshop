<?php
    include_once __DIR__ . '/../classes/Review.php';

    if (!empty($_POST)){
        $c = new Review();
        $c->setText($_POST['text']);
        $c->setPostid($_POST['postid']);
        $c->setUserid(1); //LATER MET $_SESSION


        $c->save();
        $response = [
            'status' => 'success',
            'body' => htmlspecialchars($c->getText()),
            'message' => 'Review saved'
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }

?>