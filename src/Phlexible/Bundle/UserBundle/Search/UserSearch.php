<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\UserBundle\Search;

use FOS\UserBundle\Model\UserInterface;
use Phlexible\Bundle\SearchBundle\Search\SearchResult;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * User search.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UserSearch implements SearchProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @param UserManagerInterface $userManager
     * @param string               $userClass
     */
    public function __construct(UserManagerInterface $userManager, $userClass)
    {
        $this->userManager = $userManager;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return 'ROLE_USERS';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'u';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $users = $this->userManager->search(array('term' => $query));

        $userClass = $this->userClass;
        /* @var UserInterface $createUser */
        $createUser = new $userClass();
        $createUser->setUsername('(unknown)');

        $results = [];
        foreach ($users as $user) {
            $results[] = new SearchResult(
                $user->getId(),
                $user->getDisplayName(),
                $createUser->getDisplayName(),
                $user->getCreatedAt(),
                '/bundles/phlexibleuser/icons/user.png',
                'Users Search',
                [
                    'handler' => 'users',
                    'parameters' => [
                        'userId' => $user->getId(),
                        'query' => $user->getUsername(),
                    ],
                ]
            );
        }

        return $results;
    }
}
