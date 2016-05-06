<?php
namespace NethServer\Tool;

/*
 * Copyright (C) 2016 Nethesis S.r.l.
 * 
 * This script is part of NethServer.
 * 
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Return the list of groups accordingly to SSSD provider: AD or LDAP
 *
 * @author Giacomo Sanchietti <giacomo.sanchietti@nethesis.it>
 */
class GroupProvider
{

    private $listGroupsCommand = '';
    private $platform;

    public function getGroups()
    {
        return $this->groups;
    }

    public function isReadOnly()
    {
        return (!$this->listGroupsCommand);
    }
    
    public function __construct(\Nethgui\System\PlatformInterface $platform)
    {
         $this->platform = $platform;

         # Search for installed provider
         if (file_exists('/usr/libexec/nethserver/ldap-list-groups')) {
             $columns[] = 'Actions';
             $this->listGroupsCommand = '/usr/libexec/nethserver/ldap-list-groups';
         } else if (file_exists('/usr/libexec/nethserver/ad-list-groups')) {
             $columns[] = 'Actions';
             $this->listGroupsCommand = '/usr/libexec/nethserver/ad-list-groups';
         }

        if ($this->listGroupsCommand) {
            $this->groups = json_decode(exec('/usr/bin/sudo '.$this->listGroupsCommand), TRUE);
        } else { # groups from remote server
            $this->groups = $this->platform->getDatabase('NethServer::Database::Group')->getAll();
        }
    }

}