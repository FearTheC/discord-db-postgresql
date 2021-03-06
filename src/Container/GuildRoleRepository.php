<?php
namespace FTC\Discord\Db\Postgresql\Container;

use Psr\Container\ContainerInterface;
use FTC\Discord\Db\Postgresql\GuildRoleRepository as GuildRoleRepositoryImp;
use FTC\Discord\Db\Core;

class GuildRoleRepository
{
    
    public function __invoke(ContainerInterface $container)
    {
        $database = $container->get(Core::class);

        return new GuildRoleRepositoryImp($database);
    }
    
}
