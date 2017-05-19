<?php
/**
 * Execute Migration for View & Stored Procedure
 *
 * @author tiichat@gmail.com
 */

use Cake\Filesystem\File;
use Migrations\AbstractMigration;

class <%= $name %> extends AbstractMigration
{
    /**
     * Up Method.
     *
     * @return void
     */
    public function up()
    {
<% if ($ddl_version > 1) : %>
        $this->drop();
<% endif; %>
        $this->create('<%= $up_ddl%>');
    }

    /**
     * Down Method.
     *
     * @return void
     */
    public function down()
    {
        $this->drop();
<% if (isset($down_ddl)) : %>
        $this->create('<%= $down_ddl%>');
<% endif; %>
    }

    /**
     * Create Item.
     */
    private function create(string $ddl)
    {
<% if ($action == 'view') : %>
        $count = $this->execute(
            'create view <%= $item%> as ' . $this->read($ddl)
        );
<% else : %>
        $count = $this->execute(
            'create procedure <%= $item%> ' . $this->read($ddl)
        );
<% endif; %>
    }

    /**
     * Drop Item.
     */
    private function drop()
    {
<% if ($action == 'view') : %>
        $count = $this->execute('drop view <%= $item%>;');
<% else : %>
        $count = $this->execute('drop procedure <%= $item%>;');
<% endif; %>
    }

    /**
     * read DDL file
     */
    private function read(string $fileName): string
    {
        $file = new File($fileName, false);
        $content = $file->read();
        $file->close();
        return $content;
    }
}
