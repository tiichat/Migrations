<?php
namespace Tiichat\Migrations\Shell\Task;

use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Migrations\Shell\Task\MigrationTask as CakeMigrationTask;

class TiiMigrationTask extends CakeMigrationTask
{

    /**
     * コマンドオプション名
     *
     * bin\cake bake tii_migration ViewFoo
     * といった感じで、ビューのマイグレーションファイルを bake する。
     *
     * @override
     */
    public function name()
    {
        return 'tii_migration';
    }

    /**
     * View か Sp(stored procedure) のとき、使用するテンプレートを変える。
     *
     * @override
     */
    public function template()
    {
        if ($this->useTii) {
            return 'Tiichat/Migrations.config/skeleton';
        }
        return parent::template();
    }

    /**
     * View, Stored Procedure 用のテンプレートデータを作成する。
     *
     * @override
     */
    public function templateData()
    {
        $data = parent::templateData();
        if ($this->useTii) {
            $data['name'] .= $this->version;
            $data['action'] = $this->action;
            $data['item'] = $this->item;
            $data['ddl_version'] = $this->version;
            $data['up_ddl'] = $this->fullPath($this->version);
            if ($this->version > 1) {
                $data['down_ddl'] = $this->fullPath($this->version - 1);
            }
        }
        return $data;
    }

    /**
     * bin\cake bake tii_migration ViewHoge
     * の、View*** で引っかけて処理を切り替える。
     *
     * @override
     */
    public function detectAction($name)
    {
        if (preg_match('/^(View|Sp)(.*)/', $name, $matches)) {
            $this->action = strtolower($matches[1]);
            $this->item = Inflector::underscore($name);
            $this->useTii = true;
            $this->createDdlFile();
            return [];
        }

        $this->useTii = false;
        return parent::detectAction($name);
    }

    /**
     * View, StoredProcedure 作成用の DDL ファイルを生成する。
     * 初回は、空ファイルを作成し、２回目以降は前のバージョンをコピーする。
     *
     * DDL格納用フォルダが存在しない場合は、作成する。
     */
    private function createDdlFile()
    {
        $this->ddl_dir = $this->getDdlPath();
        $dir = new Folder($this->ddl_dir, true);
        $files = $dir->find($this->item . '_.*\\' . $this->ext_ddl, true);
        $max_num = count($files);
        $this->version = $max_num + 1;
        if ($max_num == 0) {
            $file = new File($this->fullPath($this->version), true);
        } else {
            $file = new File($this->ddl_dir . DS . $files[$max_num - 1], false);
            $file->copy($this->fullPath($this->version), false);
        }
    }

    private function getDdlPath()
    {
        return $this->getPath() . 'ddl';
    }

    /**
     * DDLファイルのフルパスを返す。
     */
    private function fullPath($version): string
    {
        return $this->ddl_dir . DS . $this->item . '_' . $version . $this->ext_ddl;
    }

    /**
     * TiiMigration の対象なら true をセット
     */
    private $useTii;

    /**
     * view or sp(stored procedure)
     */
    private $action;

    /**
     * DDLを格納するフォルダ
     */
    private $ddl_dir;

    /**
     * view or stored procedure 名称
     */
    private $item;

    /**
     * DDL の拡張子
     */
    private $ext_ddl = '.ddl';

    /**
     * DDLファイルのバージョン番号
     */
    private $version;
}
