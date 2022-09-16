<?php
class Maze{
    public $width;
    public $height;
    public $maze;
    public $paths;
    private $DX;
    private $DY;

    public function __construct(){
        // directions
        // east, west, north, south
        $this->DX = [1, -1, 0, 0]; 
        $this->DY = [0, 0, -1, 1];
        $this->paths = [];
        $w = (int)$_GET['width'];
        $h = (int)$_GET['height'];
        if ($w <= 3) {
            $this->width = 3;
        } elseif ($w >= 10) {
            $this->width = 10;
        } else {
            $this->width = $w;
        }
        if ($h <= 3) {
            $this->height = 3;
        } elseif ($h >= 20) {
            $this->height = 20;
        } else {
            $this->height = $h;
        }
        $k = 0;
        for($i = 0; $i < $this->height; $i++){
            for($j = 0; $j < $this->width; $j++){
                $this->maze[$i][$j] = ["x"=>$j, "y"=>$i, "L" => array(), "visited" => false, "index"=>$k];
                $k++;
            }
        }
        $this->connections_from(0, 0);
    }

    function connections_from($current_x, $current_y){
        $this->maze[0][0]["visited"] = true;
        $directions = [0, 1, 2, 3];
        shuffle($directions);
        foreach($directions as &$direction){
            $new_x = $current_x + $this->DX[$direction];
            $new_y = $current_y + $this->DY[$direction];
            if ($new_x >= 0 && 
            $new_x < $this->width && 
            $new_y >= 0 && 
            $new_y < $this->height){
                if ($this->maze[$new_y][$new_x]["visited"] == false){
                    $this->maze[$new_y][$new_x]["visited"] = true;
                    array_push($this->maze[$current_y][$current_x]["L"], $this->maze[$new_y][$new_x]["index"]);
                    array_push($this->maze[$new_y][$new_x]["L"], $this->maze[$current_y][$current_x]["index"]);
                    $this->connections_from($new_x, $new_y);
                }
            }
            unset($new_x);
            unset($new_y);
            unset($direction);
        }
    }

    function remove_unnecesary_nodes(){
        // znajdujemy niepotrzebne punkty
        $unnecesary_nodes = [];
        $unnecesary_nodes_horizontal = [];
        $unnecesary_nodes_vertical = [];
        for($i = 0; $i < $this->height; $i++){
            for($j = 0; $j < $this->width; $j++){
                if(count($this->maze[$i][$j]["L"]) == 2){
                    if (($this->maze[$i][$j]["L"][0] + $this->maze[$i][$j]["L"][1])/2 == $this->maze[$i][$j]["index"]){
                        $is_horizontal = (abs($this->maze[$i][$j]["L"][0] - $this->maze[$i][$j]["L"][1]) == 2);
                        array_push($unnecesary_nodes, [$i, $j, $is_horizontal]);
                        if($is_horizontal){
                            array_push($unnecesary_nodes_horizontal, [$i, $j, $is_horizontal]);
                        } else {
                            array_push($unnecesary_nodes_vertical, [$i, $j, $is_horizontal]);
                        }
                    }
                }
            }
        }

        // sort vertical nodes array
        $size = count($unnecesary_nodes_vertical)-1;
        for ($a=0; $a<$size; $a++) {
            for ($b=0; $b<$size-$a; $b++) {
                $c = $b+1;
                if ($unnecesary_nodes_vertical[$c][1] < $unnecesary_nodes_vertical[$b][1]) {
                    list($unnecesary_nodes_vertical[$b], $unnecesary_nodes_vertical[$c]) = array($unnecesary_nodes_vertical[$c], $unnecesary_nodes_vertical[$b]);
                }
            }
        }

        // tylko niepotrzebne polaczenia poziome
        for($i = 0; $i < count($unnecesary_nodes_horizontal); $i++){
            $counter = 1;
            $j = $i;
            while ($j+1 < count($unnecesary_nodes_horizontal) && 
                   $unnecesary_nodes_horizontal[$j][0] == $unnecesary_nodes_horizontal[$j+1][0] && 
                   $unnecesary_nodes_horizontal[$j][1] == $unnecesary_nodes_horizontal[$j+1][1] - 1){
				$counter++;
				$j++;
				// jezeli indeks przekroczy dlugosc listy to przerwij
				if ($j + 1 >= count($unnecesary_nodes_horizontal)){
					break;
				}
			}
            // mamy ile przeskoczyc (counter)
			// mamy poczatkowy niepotrzebny punkt
			// start = (u_n[i][0], u_n[i][1] - 1)
            $start = $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] - 1];
			// koniec = (u_n[i][0], u_n[i][1] + counter)
            $koniec = $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] + $counter];
			// zmieniamy w L punktu start
			// potrzebujemy pozycje w L punktu start, punktu u_n[i]
			// nazwiemy ten indeks: index1
            $node_index = $unnecesary_nodes_horizontal[$i][0]*$this->width + $unnecesary_nodes_horizontal[$i][1];
            $index_one = array_search($node_index, $start["L"]);
			// potrzbujemy pozycje w L punktu koniec, punktu u_n[i + counter - 1]
			// nazwiemy ten indeks: index2
            $node_index = $unnecesary_nodes_horizontal[$i + $counter - 1][0]*$this->width + $unnecesary_nodes_horizontal[$i + $counter - 1][1];
            $index_two = array_search($node_index, $koniec["L"]);
			// wtedy start.L[index1] = koniec.index
			// koniec.L[index2] = start.index
            $start["L"][$index_one] = $koniec["index"];
            $koniec["L"][$index_two] = $start["index"];
            $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] - 1]["L"][$index_one] = $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] + $counter]["index"];
            $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] + $counter]["L"][$index_two] = $this->maze[$unnecesary_nodes_horizontal[$i][0]][$unnecesary_nodes_horizontal[$i][1] - 1]["index"];
            $i += ($counter - 1);
        }
        // tylko niepotrzebne polaczenia pionowe
        for($i = 0; $i < count($unnecesary_nodes_vertical); $i++){
            $counter = 1;
            $j = $i;
            while ($j+1 < count($unnecesary_nodes_vertical) && 
                   $unnecesary_nodes_vertical[$j][1] == $unnecesary_nodes_vertical[$j+1][1] && 
                   $unnecesary_nodes_vertical[$j][0] == $unnecesary_nodes_vertical[$j+1][0] - 1){
				$counter++;
				$j++;
				// jezeli indeks przekroczy dlugosc listy to przerwij
				if ($j + 1 >= count($unnecesary_nodes_vertical)){
					break;
				}
			}
            // mamy ile przeskoczyc (counter)
			// mamy poczatkowy niepotrzebny punkt
			// start = (u_n[i][0], u_n[i][1] - 1)
            $start = $this->maze[$unnecesary_nodes_vertical[$i][0] - 1][$unnecesary_nodes_vertical[$i][1]];
			// koniec = (u_n[i][0], u_n[i][1] + counter)
            $koniec = $this->maze[$unnecesary_nodes_vertical[$i][0] + $counter][$unnecesary_nodes_vertical[$i][1]];
			// zmieniamy w L punktu start
			// potrzebujemy pozycje w L punktu start, punktu u_n[i]
			// nazwiemy ten indeks: index1
            $node_index = $unnecesary_nodes_vertical[$i][0]*$this->width + $unnecesary_nodes_vertical[$i][1];
            $index_one = array_search($node_index, $start["L"]);
			// potrzbujemy pozycje w L punktu koniec, punktu u_n[i + counter - 1]
			// nazwiemy ten indeks: index2
            $node_index = $unnecesary_nodes_vertical[$i + $counter - 1][0]*$this->width + $unnecesary_nodes_vertical[$i + $counter - 1][1];
            $index_two = array_search($node_index, $koniec["L"]);
			// wtedy start.L[index1] = koniec.index
			// koniec.L[index2] = start.index
            $start["L"][$index_one] = $koniec["index"];
            $koniec["L"][$index_two] = $start["index"];
            $this->maze[$unnecesary_nodes_vertical[$i][0] - 1][$unnecesary_nodes_vertical[$i][1]]["L"][$index_one] = $this->maze[$unnecesary_nodes_vertical[$i][0] + $counter][$unnecesary_nodes_vertical[$i][1]]["index"];
            $this->maze[$unnecesary_nodes_vertical[$i][0] + $counter][$unnecesary_nodes_vertical[$i][1]]["L"][$index_two] = $this->maze[$unnecesary_nodes_vertical[$i][0] - 1][$unnecesary_nodes_vertical[$i][1]]["index"];
            $i += ($counter - 1);
        }

        // usuwanie niepotrzebnych polaczen

        $cur_row = $unnecesary_nodes[0][0];
        $counter = 0;
        for($i = 0; $i < count($unnecesary_nodes); $i++){
            if($unnecesary_nodes[$i][0] == $cur_row){
                array_splice($this->maze[$cur_row], $unnecesary_nodes[$i][1] - $counter, 1);
                $counter++;
            } else {
                $counter = 0;
                $cur_row = $unnecesary_nodes[$i][0];
                array_splice($this->maze[$cur_row], $unnecesary_nodes[$i][1], 1);
                $counter++;
            }
        }
    }

    function convert_paths(){
        // zmieniamy indeksy punktow na siatce, na indeksy polaczen
        for ($i = 0; $i < count($this->paths); $i++){
            for($j = 0; $j < count($this->paths[$i]["L"]); $j++){
                $index = $this->paths[$i]["L"][$j];
                $row = floor($index / $this->width);
                $column = $index % $this->width;
                // znajdujemy odpowiedni punkt
                for ($n = 0; $n < count($this->paths); $n++){
                    if ($this->paths[$n]["x"] == $column && $this->paths[$n]["y"] == $row){
                        $this->paths[$i]["L"][$j] = $n;
                    }
                }
            }
        }
    }

    function json(){
        $this->remove_unnecesary_nodes();
        $k = 0;  
        for($i = 0; $i < count($this->maze); $i++){
            for($j = 0; $j < count($this->maze[$i]); $j++){
                $this->paths[$k]=["x" => $this->maze[$i][$j]["x"], "y" => $this->maze[$i][$j]["y"], "L" => $this->maze[$i][$j]["L"]];
                $k++;
            }
        }
        $result = ["width" => $this->width,"height" => $this->height,"paths" => $this->paths];
        $this->convert_paths();
        $result = ["width" => $this->width,"height" => $this->height,"paths" => $this->paths];
        $myJSON = json_encode($result);
        return $myJSON;
    }
}

header('Access-Control-Allow-Origin: *');
if(isset($_GET['width']) && isset($_GET['height'])){
    $obj = new Maze();
    print_r($obj->json());
}
?>