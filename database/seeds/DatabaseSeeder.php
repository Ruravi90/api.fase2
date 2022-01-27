<?php

use Illuminate\Database\Seeder;
use Caffeinated\Shinobi\Models\Role;
use Caffeinated\Shinobi\Models\Permission;
use fase2\User;
use fase2\CatExpense;
use fase2\CatTypeSale;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// permisos roles
     	Permission::updateOrCreate(
     		['slug' => 'module_rol'],
     		['name' => 'Ver modulo de roles','slug' => 'module_rol','description' => 'El usuario puede ver el modulo de roles']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_rol'],
     		['name' => 'Agregar roles','slug' => 'add_rol','description' => 'El usuario puede agregar roles']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_rol'],
     		['name' => 'Editar usuarios','slug' => 'edit_rol','description' => 'El usuario puede editar roles']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_rol'],
     		['name' => 'Eliminar roles','slug' => 'delete_rol','description' => 'El usuario puede eliminar roles']
     	);
     	// permisos usuarios
     	Permission::updateOrCreate(
     		['slug' => 'module_user'],
     		['name' => 'Ver modulo de usuarios','slug' => 'module_user','description' => 'El usuario puede ver el modulo de usuarios']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_user'],
     		['name' => 'Agregar usuarios','slug' => 'add_user','description' => 'El usuario puede agregar usuarios']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_user'],
     		['name' => 'Editar usuarios','slug' => 'edit_user','description' => 'El usuario puede editar usuarios']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_user'],
     		['name' => 'Eliminar usuarios','slug' => 'delete_user','description' => 'El usuario puede eliminar usuarios']
     	);
     	// permisos clientes
     	Permission::updateOrCreate(
     		['slug' => 'module_client'],
     		['name' => 'Ver modulo de clientes','slug' => 'module_client','description' => 'El usuario puede ver el modulo de clientes']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_client'],
     		['name' => 'Agregar clientes','slug' => 'add_client','description' => 'El usuario puede agregar clientes']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_client'],
     		['name' => 'Editar clientes','slug' => 'edit_client','description' => 'El usuario puede editar clientes']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_client'],
     		['name' => 'Eliminar clientes','slug' => 'delete_client','description' => 'El usuario puede eliminar clientes']
     	);
     	// permisos proveedores
     	Permission::updateOrCreate(
     		['slug' => 'module_provider'],
     		['name' => 'Ver modulo de proveedores','slug' => 'module_provider','description' => 'El usuario puede ver el modulo de proveedores']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_provider'],
     		['name' => 'Agregar proveedores','slug' => 'add_provider','description' => 'El usuario puede agregar proveedores']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_provider'],
     		['name' => 'Editar proveedores','slug' => 'edit_provider','description' => 'El usuario puede editar proveedores']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_provider'],
     		['name' => 'Eliminar proveedores','slug' => 'delete_provider','description' => 'El usuario puede eliminar proveedores']
     	);
     	// permisos acreedores
     	Permission::updateOrCreate(
     		['slug' => 'module_creditor'],
     		['name' => 'Ver modulo de acreedores','slug' => 'module_creditor','description' => 'El usuario puede ver el modulo de acreedores']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_creditor'],
     		['name' => 'Agregar acreedores','slug' => 'add_creditor','description' => 'El usuario puede agregar acreedores']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_creditor'],
     		['name' => 'Editar acreedores','slug' => 'edit_creditor','description' => 'El usuario puede editar acreedores']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_creditor'],
     		['name' => 'Eliminar acreedores','slug' => 'delete_creditor','description' => 'El usuario puede eliminar acreedores']
     	);
     	// permisos inventarios productos
     	Permission::updateOrCreate(
     		['slug' => 'module_product_inventory'],
     		['name' => 'Ver modulo de inventario de productos','slug' => 'module_product_inventory','description' => 'El usuario puede ver el modulo de inventario de productos']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_product_inventory'],
     		['name' => 'Agregar productos al inventario','slug' => 'add_product_inventory','description' => 'El usuario puede agregar productos al inventario']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_product_inventory'],
     		['name' => 'Editar productos del inventario','slug' => 'edit_product_inventory','description' => 'El usuario puede editar productos del inventario']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_product_inventory'],
     		['name' => 'Eliminar productos del inventario','slug' => 'delete_product_inventory','description' => 'El usuario puede eliminar productos del inventario']
     	);
     	// permisos inventario pastillas
     	Permission::updateOrCreate(
     		['slug' => 'module_pill_inventory'],
     		['name' => 'Ver modulo de inventario de pastillas','slug' => 'module_pill_inventory','description' => 'El usuario puede ver el modulo de inventario de pastillas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_pill_inventory'],
     		['name' => 'Agregar pastillas al inventario','slug' => 'add_pill_inventory','description' => 'El usuario puede agregar pastillas al inventario']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_pill_inventory'],
     		['name' => 'Editar pastillas del inventario','slug' => 'edit_pill_inventory','description' => 'El usuario puede editar pastillas del inventario']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_pill_inventory'],
     		['name' => 'Eliminar pastillas del inventario','slug' => 'delete_pill_inventory','description' => 'El usuario puede eliminar pastillas del inventario']
     	);
    		// permisos catalogo de referencias
     	Permission::updateOrCreate(
     		['slug' => 'module_cat_reference'],
     		['name' => 'Ver modulo de catalogo de referencias','slug' => 'module_cat_reference','description' => 'El usuario puede ver el modulo de catalogo de referencias']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_cat_reference'],
     		['name' => 'Agregar referencias','slug' => 'add_cat_reference','description' => 'El usuario puede agregar referencias']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_cat_reference'],
     		['name' => 'Editar referencias','slug' => 'edit_cat_reference','description' => 'El usuario puede editar referencias']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_cat_reference'],
     		['name' => 'Eliminar referencias','slug' => 'delete_cat_reference','description' => 'El usuario puede eliminar referencias']
     	);
     	// permisos catalogo de paquetes
     	Permission::updateOrCreate(
     		['slug' => 'module_cat_package'],
     		['name' => 'Ver modulo de catalo de paquetes','slug' => 'module_cat_package','description' => 'El usuario puede ver el modulo de catalo de paquetes']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_cat_package'],
     		['name' => 'Agregar paquetes','slug' => 'add_cat_package','description' => 'El usuario puede agregar paquetes']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_cat_package'],
     		['name' => 'Editar paquetes','slug' => 'edit_cat_package','description' => 'El usuario puede editar paquetes']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_cat_package'],
     		['name' => 'Eliminar paquetes','slug' => 'delete_cat_package','description' => 'El usuario puede eliminar paquetes']
     	);
     	// permisos catalogo de productos
     	Permission::updateOrCreate(
     		['slug' => 'module_cat_product'],
     		['name' => 'Ver modulo de catalogo de productos','slug' => 'module_cat_product','description' => 'El usuario puede ver el modulo de catalogo de productos']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_cat_product'],
     		['name' => 'Agregar productos','slug' => 'add_cat_product','description' => 'El usuario puede agregar productos']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_cat_product'],
     		['name' => 'Editar productos','slug' => 'edit_cat_product','description' => 'El usuario puede editar productos']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_cat_product'],
     		['name' => 'Eliminar productos','slug' => 'delete_cat_product','description' => 'El usuario puede eliminar productos']
     	);
     	// permisos catalogo de pastillas
     	Permission::updateOrCreate(
     		['slug' => 'module_cat_pill'],
     		['name' => 'Ver modulo de catalogo de pastillas','slug' => 'module_cat_pill','description' => 'El usuario puede ver el modulo de catalogo de pastillas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_cat_pill'],
     		['name' => 'Agregar pastillas','slug' => 'add_cat_pill','description' => 'El usuario puede agregar pastillas']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_cat_pill'],
     		['name' => 'Editar pastillas','slug' => 'edit_cat_pill','description' => 'El usuario puede editar pastillas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_cat_pill'],
     		['name' => 'Eliminar pastillas','slug' => 'delete_cat_pill','description' => 'El usuario puede eliminar pastillas']
     	);
     	// permisos ventas
     	Permission::updateOrCreate(
     		['slug' => 'module_sale'],
     		['name' => 'Ver modulo de ventas','slug' => 'module_sale','description' => 'El usuario puede ver el modulo de ventas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_sale'],
     		['name' => 'Agregar ventas','slug' => 'add_sale','description' => 'El usuario puede agregar ventas']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_sale'],
     		['name' => 'Editar ventas','slug' => 'edit_sale','description' => 'El usuario puede editar ventas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_sale'],
     		['name' => 'Eliminar ventas','slug' => 'delete_sale','description' => 'El usuario puede eliminar ventas']
     	);
     	// permisos agenda
     	Permission::updateOrCreate(
     		['slug' => 'module_schedule'],
     		['name' => 'Ver modulo de agenda','slug' => 'module_schedule','description' => 'El usuario puede ver el modulo de agenda']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'add_schedule'],
     		['name' => 'Agregar citas','slug' => 'add_schedule','description' => 'El usuario puede agregar citas']
     	);
    	Permission::updateOrCreate(
     		['slug' => 'edit_schedule'],
     		['name' => 'Editar citas','slug' => 'edit_schedule','description' => 'El usuario puede editar citas']
     	);
     	Permission::updateOrCreate(
     		['slug' => 'delete_schedule'],
     		['name' => 'Eliminar citas','slug' => 'delete_schedule','description' => 'El usuario puede eliminar citas']
     	);
        Permission::updateOrCreate(
            ['slug' => 'module_package'],
            ['name' => 'Ver modulo de paquetes','slug' => 'module_package','description' => 'El usuario puede ver el modulo de paquetes']
        );
        Permission::updateOrCreate(
            ['slug' => 'module_purchases'],
            ['name' => 'Ver modulo de compras','slug' => 'module_purchases','description' => 'El usuario puede ver el modulo de compras']
		);
		Permission::updateOrCreate(
            ['slug' => 'module_box'],
            ['name' => 'Ver modulo de caja','slug' => 'module_box','description' => 'El usuario puede ver el modulo de caja']
        );

     	//Roles del sistema
     	Role::updateOrCreate(
     		['slug' => 'super_admin'],
     		['name' => 'Super administrador','slug' => 'super_admin','description' => 'Control total del sistema']
     	);
     	Role::updateOrCreate(
     		['slug' => 'admin'],
     		['name' => 'Administrador','slug' => 'admin','description' => 'Control parcial del sistema']
     	);
		Role::updateOrCreate(
     		['slug' => 'user'],
     		['name' => 'Usuario','slug' => 'user','description' => 'Control restringido del sistema']
		 );
		 
		Role::updateOrCreate(
			['slug' => 'agent'],
			['name' => 'Agente','slug' => 'agent','description' => 'Control restringido del sistema']
		);


     	//Asignar roles
      $rol = Role::where('slug','super_admin')->first();
      $rol->revokeAllPermissions();
      $Permissions = Permission::all();
     	foreach ($Permissions as $key => $value) {
        $rol->assignPermission($value["id"]);
      }
      $rol->save();


      //Asignar roles
      $rol = Role::where('slug','admin')->first();
      $rol->revokeAllPermissions();
      $Permissions = Permission::all();
      foreach ($Permissions as $key => $value) {
            $rol->assignPermission($value["id"]);
      }
      $rol->save();

      User::updateOrCreate(
            ['username' => 'raguilar'],
            ['name' => 'Ruravi','lastname' => 'Aguilar','motherlastname' => 'Arrezola','email' => 'ruravi.app@gmail.com','password' => bcrypt('ruravi90')]
      );
            
      $user = User::where('username','raguilar')->first();
      $user->revokeAllRoles();
      $user->assignRole(Role::where('slug','admin')->first()->id);
      $user->assignRole(Role::where('slug','super_admin')->first()->id);
      $user->save();

      User::updateOrCreate(
            ['username' => 'jcuevas'],
            ['name' => 'Joel','lastname' => 'Cuevas','motherlastname' => '','email' => 'jcuqevas@fase2spa.com.mx','password' => bcrypt('jcuevas')]
      );
            
      $user = User::where('username','jcuevas')->first();
      $user->revokeAllRoles();
      $user->assignRole(Role::where('slug','admin')->first()->id);
      $user->save();

	  	User::updateOrCreate(
			['username' => 'agente1'],
			['name' => 'Agente','lastname' => 'prueba','email' => 'agente@fase2spa.com.mx','password' => bcrypt('agente1')]
		);

		$user = User::where('username','agente1')->first();
		$user->revokeAllRoles();
		$user->assignRole(Role::where('slug','agent')->first()->id);
		$user->save();

      CatExpense::updateOrCreate(
            ['name' => 'Pastillas'],
            ['name' => 'Pastillas']
      );

      CatExpense::updateOrCreate(
            ['name' => 'Productos'],
            ['name' => 'Productos']
      );

	  CatTypeSale::updateOrCreate(
		['name' => 'Efectivo'],
		['name' => 'Efectivo']
	  );
	  CatTypeSale::updateOrCreate(
		['name' => 'Debito'],
		['name' => 'Debito']
	  );
	  CatTypeSale::updateOrCreate(
		['name' => 'Credito'],
		['name' => 'Credito']
	  );
    }
}
