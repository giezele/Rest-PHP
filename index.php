<?php
// DUOMENYS
class Author implements JsonSerializable {
        private $id;
        private $name;
        private $surname;
        public function __construct($id, $name, $surname){
            $this->id = $id;
            $this->name = $name;
            $this->surname = $surname;
        }
        public function getId(){ return $this->id; }
        public function setId($id){ $this->id = $id; }
        public function getName(){ return $this->name; }
        public function setName($name){ $this->name = $name;}
        public function getSurname(){ return $this->surname; }
        public function setSurname($surname){ $this->surname = $surname;}
        
        public function jsonSerialize() {
            $vars = get_object_vars($this);
            return $vars;
        }
    }

class Book implements JsonSerializable {
    private $isbn;  
    private $title;
    private $authors = [];
    public function __construct($isbn, $title, $authors){
        $this->isbn = $isbn;
        $this->title = $title;
        $this->authors = $authors;
    }
    public function getIsbn(){ return $this->isbn; }
    public function setIsbn($isbn){ $this->isbn = $isbn; }
    public function getTitle(){ return $this->title; }
    public function setTitle($title){ $this->title = $title;}
    public function getAuthors(){ return $this->title; }
    public function setAuthors($authors){ $this->authors = $authors;}

    public function jsonSerialize() {
        $vars = get_object_vars($this);
        return $vars;
    }
}
    


// Read JSON file
$FILE = 'authors.json';
// Json stringą verčiame assoc masyvu
$json_file = file_get_contents($FILE);
$authors_arr = json_decode($json_file, true);

// print_r($authors_arr); 

// create authors (objects!) from file
$authors = [];
foreach($authors_arr as $author)
    array_push($authors, new Author(
        $author['id'], 
        $author['name'],
        $author['surname']
    ));

// var_dump($authors); // no dd() no problem b/c no entities

// LOGIKA
switch($_SERVER['REQUEST_METHOD']){
    //sukurti naują autorių
    // cURL komanda
    // $ curl http://localhost/REST/authors -s -X POST -d '{"id":"4", "name":"Vytautas", "surname":"Suopis"}'
    case 'POST': 
    if($_SERVER['REQUEST_URI'] === "/REST/authors"){
        $inputJSON = file_get_contents('php://input');
        $x = json_decode($inputJSON, true);
        array_push($authors, new Author($x['id'], $x['name'],$x['surname']));
        $people_arr = json_encode($authors);
        file_put_contents($FILE, $people_arr);
    }
    break;

    //gauti autoriaus informaciją pagal id ARBA gauti visų autorių informaciją jei be {id}
    // $ curl http://localhost/REST/authors -s -X GET
    // HTTP/1.1 200 OK
    // Date: Mon, 12 Jul 2021 09:31:49 GMT
    // Array
        // (
        //     [0] => Author Object
        //         (
        //             [id:Author:private] => 1
        //             [name:Author:private] => Marytė
        //             [surname:Author:private] => Melnikaitė
        //         )

        //     [1] => Author Object
        //         (
        //             [id:Author:private] => 2
        //             [name:Author:private] => Ignius
        //             [surname:Author:private] => Knyguolis
        //         )

        //     [2] => Author Object
        //         (
        //             [id:Author:private] => 3
        //             [name:Author:private] => Jonas
        //             [surname:Author:private] => Biliunas
        //         )

        // )
    case 'GET': 
        if($_SERVER['REQUEST_URI'] === "/REST/authors"){
            print_r($authors);
        } elseif (substr($_SERVER['REQUEST_URI'], 0, strlen("/REST/authors/")) === "/REST/authors/"){
            $id = end(explode('/', $_SERVER['REQUEST_URI']));
            foreach($authors as $author){
                if($author->getId() == $id)
                    print_r($author);
            }
        }
        // print($_SERVER['REQUEST_URI']);
        break;

    //modifikuoti autoriaus info
    //$ curl localhost/rest/authors/{id} -D - -s -X PUT
    case 'PUT': print("PUT"); break;

    //trinti autoriu
    //$ curl localhost/rest/authors/{id} -D - -s -X DELETE
    case 'DELETE': print("DELETE"); break;

    default: die("HTTP verb unknown!");
}

// Helper function
function prnt($x){
echo json_encode($x, JSON_UNESCAPED_UNICODE);
}
