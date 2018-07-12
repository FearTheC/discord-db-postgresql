<?php declare(strict_types=1);

namespace FTC\Discord\Db\Postgresql;


use FTC\Discord\Model\Aggregate\GuildRepository as RepositoryInterface;
use FTC\Discord\Model\Aggregate\Guild;
use FTC\Discord\Model\ValueObject\Snowflake\GuildId;
use FTC\Discord\Model\ValueObject\DomainName;
use FTC\Discord\Db\Postgresql\Mapper\GuildMapper;

class GuildRepository extends PostgresqlRepository implements RepositoryInterface
{

    const SELECT_GUILD = <<<'EOT'
SELECT * from guilds_aggregates
WHERE id = :id;
EOT;

    const SELECT_GUILD_BY_DOMAIN_NAME = <<<'EOT'
SELECT id, name, owner_id, domain, members_ids, roles_ids, channels_ids FROM guilds_aggregates
WHERE domain = :domain_name
EOT;

    const SELECT_GUILD_MEMBER = <<<'EOT'
SELECT guilds_users.user_id as id, guilds_users.nickname, guilds_users.joined_date, json_agg(members_roles.role_id) AS roles_ids FROM guilds_users
JOIN members_roles ON members_roles.user_id = guilds_users.user_id
where guilds_users.guild_id = :guild_id AND guilds_users.user_id = :member_id
GROUP BY guilds_users.user_id, guilds_users.nickname, guilds_users.joined_date
EOT;

    const INSERT_GUILD = "INSERT INTO guilds VALUES (:id, :name, :owner_id) ON CONFLICT (id) DO UPDATE SET name = :name, owner_id = :owner_id";
    
    const INSERT_GUILD_MEMBER = <<<'EOT'
INSERT INTO guilds_users VALUES (:id, :user_id, :nickname, :joined_at)
ON CONFLICT (guild_id, user_id) DO UPDATE SET nickname = :nickname
EOT;

    const INSERT_GUILD_MEMBER_ROLES = <<<'EOT'
INSERT INTO users_roles VALUES (:user_id, :role_id)
ON CONFLICT (user_id, role_id) DO NOTHING
EOT;

    /**
     * @var Guild[]
     */
    private $guilds;
    
    public function save(Guild $guild)
    {
        $stmt = $this->persistence->prepare(self::INSERT_GUILD);
        $stmt->bindValue('id', $guild->getId()->get(), \PDO::PARAM_INT);
        $stmt->bindValue('name', (string) $guild->getName(), \PDO::PARAM_STR);
        $stmt->bindValue('owner_id', $guild->getOwnerId()->get(), \PDO::PARAM_INT);
        $stmt->execute();
    }
    
    public function getAll() : array
    {
        
    }
    
    public function findByDomainName(DomainName $domainName) : ?Guild
    {
        $stmt = $this->persistence->prepare(self::SELECT_GUILD_BY_DOMAIN_NAME);
        $stmt->bindValue('domain_name', (string) $domainName, \PDO::PARAM_STR);
        $stmt->execute();
        
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$data) {
            return null;
        }
        
        return GuildMapper::create($data);
    }
    
    public function findById(GuildId $id) : ?Guild
    {
//         $stmt = $this->persistence->prepare(self::SD);
//         $stmt->execute();
        
//         $stmt = $this->persistence->prepare(self::SELECT_GUILD);
//         $stmt->bindValue('id', $id->get(), \PDO::PARAM_INT);
//         $stmt->execute();
//         $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
//         $guildId = Snowflake::create($row['id']);
        
        
//         $members = new GuildMemberCollection();
//         $roles = new GuildRoleCollection();
        
//         foreach (json_decode($row['roles'], true) as $role) {
//             $roles->add(GuildRole::create(Snowflake::create($role['id']), $role['name']));
//         }
        
//         foreach (json_decode($row['members'], true) as $member) {
//             $members->add(GuildMember::create($guildId, UserId::create($member['user_id']), $roles,'nickname'));
//         }

//         $guild = Guild::create(
//             $guildId,
//             $row['name'],
//             Snowflake::create(272341331328761888),
//             $roles,
//             $members);

//         return $guild;
    }



}
