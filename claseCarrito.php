<?php 
session_start();

class Carrito {
    protected $cart_contents = array();
    
    public function __construct(){
        // get the shopping cart array from the session
        $this->cart_contents = !empty($_SESSION['cart_contents'])?$_SESSION['cart_contents']:NULL;
		if ($this->cart_contents === NULL){
			// set some base values
			$this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
		}
    }
    
    /**
	 * Cart Contents: Returns the entire cart array
	 * @param	bool
	 * @return	array
	 */
	public function contents(){
		// rearrange the newest first
		$carrito = array_reverse($this->cart_contents);

		// remove these so they don't create a problem when showing the cart table
		unset($carrito['total_items']);
		unset($carrito['cart_total']);

		return $carrito;
	}
    
    /**
	 * Get cart item: Returns a specific cart item details
	 * @param	string	$row_id
	 * @return	array
	 */
	public function get_item($codigo){
		return (in_array($codigo, array('total_items', 'cart_total'), TRUE) OR ! isset($this->cart_contents[$codigo]))
			? FALSE
			: $this->cart_contents[$codigo];
	}
    
    /**
	 * Total Items: Returns the total item count
	 * @return	int
	 */
	public function total_items(){
		return $this->cart_contents['total_items'];
	}
    
    /**
	 * Cart Total: Returns the total price
	 * @return	int
	 */
	public function total(){
		$this->cart_contents['cart_total'] = 0;
		$this->cart_contents['total_items'] = 0;
		foreach ($this->cart_contents as $item) {
			// Asegúrate de que $item es un array antes de intentar acceder a sus índices
			if(is_array($item) && isset($item['subtotal'], $item['cantidad'])) {
				$this->cart_contents['cart_total'] += $item['subtotal'];
				$this->cart_contents['total_items'] += $item['cantidad'];
    		}
		}

		return isset($this->cart_contents['cart_total']) ? $this->cart_contents['cart_total'] : 0;
	}
    
    /**
	 * Insert items into the cart and save it to the session
	 * @param	array
	 * @return	bool
	 */
	public function insert($item = array()){
		if(!is_array($item) OR count($item) === 0){
			return FALSE;
		}else{
            if(!isset($item['codigo'], $item['nombre'], $item['precio'], $item['cantidad'])){
                return FALSE;
            }else{
                /*
                 * Insert Item
                 */
                // prep the quantity
                $item['cantidad'] = (float) $item['cantidad'];
                if($item['cantidad'] == 0){
                    return FALSE;
                }
                // prep the price
                $item['precio'] = (float) $item['precio'];
                // create a unique identifier for the item being inserted into the cart
                $rowcodigo = md5($item['codigo']);
                // get quantity if it's already there and add it on
                $old_cantidad = isset($this->cart_contents[$rowcodigo]['cantidad']) ? (int) $this->cart_contents[$rowcodigo]['cantidad'] : 0;
                // re-create the entry with unique identifier and updated quantity
                $item['rowcodigo'] = $rowcodigo;
                $item['cantidad'] += $old_cantidad;
                $this->cart_contents[$rowcodigo] = $item;
                
                // save Cart Item
                if($this->save_cart()){
                    return isset($rowcodigo) ? $rowcodigo : TRUE;
                }else{
                    return FALSE;
                }
            }
        }
	}
    
    /**
	 * Update the cart
	 * @param	array
	 * @return	bool
	 */
	public function update($item = array()){
		if (!is_array($item) OR count($item) === 0){
			return FALSE;
		} else {
			if (!isset($item['codigo'], $this->cart_contents[$item['codigo']])){
				return FALSE;
			} else {
				// prep the quantity
				if(isset($item['cantidad'])){
					$item['cantidad'] = (float) $item['cantidad'];
					// remove the item from the cart, if quantity is zero
					if ($item['cantidad'] == 0){
						unset($this->cart_contents[$item['codigo']]);
						return TRUE;
					}
				}
				
				// find updatable keys
				$keys = array_intersect(array_keys($this->cart_contents[$item['codigo']]), array_keys($item));
				// prep the price
				if(isset($item['precio'])){
					$item['precio'] = (float) $item['precio'];
				}
				// product id & name shouldn't be changed
				foreach(array_diff($keys, array('codigo', 'nombre')) as $key){
					$this->cart_contents[$item['codigo']][$key] = $item[$key];
				}
				// save cart data
				$this->save_cart();
				return TRUE;
			}
		}
	}
	
    
    /**
	 * Save the cart array to the session
	 * @return	bool
	 */
	protected function save_cart(){
		$this->cart_contents['total_items'] = $this->cart_contents['cart_total'] = 0;
        foreach ($this->cart_contents as $key => $val){
            // make sure the array contains the proper indexes
            if(!is_array($val) OR !isset($val['precio'], $val['cantidad'], $val['descuento'])){
                continue;
            }
     
            // Calculate subtotal with discount applied
            $precioConDescuento = $val['precio'] * (1 - $val['descuento']);
            $this->cart_contents[$key]['subtotal'] = $precioConDescuento * $val['cantidad'];

            // Update cart total
            $this->cart_contents['cart_total'] += $this->cart_contents[$key]['subtotal'];
            $this->cart_contents['total_items'] += $val['cantidad'];
        }
        
        // if cart empty, delete it from the session
        if(count($this->cart_contents) <= 2){
            unset($_SESSION['cart_contents']);
            return FALSE;
        }else{
            $_SESSION['cart_contents'] = $this->cart_contents;
            return TRUE;
        }
    }
    
    /**
	 * Remove Item: Removes an item from the cart
	 * @param	int
	 * @return	bool
	 */
	 public function remove($codigo){
		// unset & save
		unset($this->cart_contents[$codigo]);
		$this->save_cart();
		return TRUE;
	 }
     
    /**
	 * Destroy the cart: Empties the cart and destroy the session
	 * @return	void
	 */
	public function destroy(){
		$this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
		unset($_SESSION['cart_contents']);
	}
}