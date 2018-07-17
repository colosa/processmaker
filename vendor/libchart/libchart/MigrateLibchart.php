<?php

// Migrates the codebase to a Composer compatible version
// using PSR-0 class loading

namespace {

	exit("\n" . 'Run this in the root directory of libchart to migrate the library to be PSR-0 / Composer compatible. '
			. "\n\n" . 'To start migration remove exit command in MigrateLibchart.php and run "php MigrateLibchart.php"' . "\n\n");

	class Migrate {


		public function migrateCode() {

			$oldClassesDirectory = 'libchart/classes/';
			$newClassesDirectory = 'libchart/Libchart/';

			if (!is_dir($newClassesDirectory)) {
				$oldClassesDirectory = new \Resource\Directory($oldClassesDirectory);
				$oldClassesDirectory->rename($newClassesDirectory);
			}

			/** @var \Resource\Directory $classesDirectory */
			$classMigration = new \Migrate\ClassMigration();
			$classesDirectoryReader = new Resource\DirectoryReader($newClassesDirectory);
			foreach ($classesDirectoryReader->getDirectories() as $classesDirectory) {
				$classMigration->migrateClassesRecursive($classesDirectory, array('Libchart'));
			}

			$mainDirectoryReader = new Resource\DirectoryReader('./');
			$codeMigration = new \Migrate\CodeMigration($classMigration);
			$codeMigration->migrateCodeRecursiveley($mainDirectoryReader);


			$fileMigration = new \Migrate\FileMigration();
			$fileMigration->migrateIncludeFiles();
		}

	}

	$migrate = new Migrate();
	$migrate->migrateCode();
}

namespace Resource {

	class ResourceUtility {

		public static function fixRightSlash($path) {
			$path = rtrim($path, '/') . '/';
			return $path;
		}
	}

	class DirectoryReader {

		protected $excludedFiles = array(
			'composer.json',
			'MigrateLibchart.php',
		);

		protected $excludedDirectories = array(
			'vendor',
			'.idea'
		);

		protected $path;

		protected $directories;

		protected $files;

		public function __construct($path) {
			if (!is_dir($path)) {
				throw new \InvalidArgumentException('Given path is not a directory. DirectoryReader can not work on invalid paths: ' . $path);
			}
			$this->path = ResourceUtility::fixRightSlash($path);
		}

		public function getDirectories() {
			if (!is_array($this->directories)) {
				$this->initialize();
			}
			return $this->directories;
		}

		public function getFiles() {
			if (!is_array($this->files)) {
				$this->initialize();
			}
			return $this->files;
		}

		protected function initialize() {

			$directoryHandle = opendir($this->path);
			$this->directories = array();
			$this->files = array();

			while (($readdir = readdir($directoryHandle)) !== FALSE) {

				if ($readdir == '.' || $readdir == '..') {
					continue;
				}

				$fullPath = $this->path . $readdir;

				if (is_file($fullPath)) {

					if (in_array($readdir, $this->excludedFiles)) {
						continue;
					}

					$this->files[] = new File($fullPath);

				} elseif (is_dir($fullPath)) {

					if (in_array($readdir, $this->excludedDirectories)) {
						continue;
					}

					$this->directories[] = new Directory($fullPath);
				}
			}

			closedir($directoryHandle);
		}


	}

	abstract class AbstractResource {

		protected $path;

		public function getPath() {
			return $this->path;
		}

		public function getLastPathPart() {
			$pathParts = $this->getPathParts();
			$lastPartIndex = \Utility\ArrayUtility::getLastArrayIndex($pathParts);
			return $pathParts[$lastPartIndex];
		}

		public function getPathParts() {
			$path = rtrim($this->getPath(), '/');
			return explode('/', $path);
		}

		public function rename($newPath) {
			$result = rename($this->getPath(), $newPath);
			if (!$result) {
				throw new \RuntimeException('Error renaming resource %s to %s');
			}
			$this->path = $newPath;
		}
	}

	class Directory extends AbstractResource {

		public function __construct($path) {
			if (!is_dir($path)) {
				throw new \InvalidArgumentException('Given path is not a directory. Directory can not work on invalid paths: ' . $path);
			}
			$this->path = $this->path = ResourceUtility::fixRightSlash($path);
		}

		public function rename($newPath) {
			$newPath = ResourceUtility::fixRightSlash($newPath);
			parent::rename($newPath);
		}

		public function renameToUcFirst() {

			$targetDirectoryName = ucfirst($this->getLastPathPart());

			if ($this->getLastPathPart() === $targetDirectoryName) {
				return;
			}

			$newPathParts = $this->getPathParts();
			$lastPathPartIndex = \Utility\ArrayUtility::getLastArrayIndex($newPathParts);
			$newPathParts[$lastPathPartIndex] = $targetDirectoryName;
			$this->rename(implode('/', $newPathParts));
		}
	}

	class File extends AbstractResource {

		public function __construct($path, $touchIfNotExists = FALSE) {

			if ($touchIfNotExists && !is_file($path)) {
				$touchResult = touch($path);
				if (!$touchResult) {
					throw new \RuntimeException('Could not touch ' . $path);
				}
			}

			if (!is_file($path)) {
				throw new \InvalidArgumentException('Given path is not a file. File can not work on invalid paths: ' . $path);
			}

			$this->path = $path;
		}

		public function getContent() {
			return file_get_contents($this->getPath());
		}

		public function replaceContent($content) {
			file_put_contents($this->getPath(), $content);
		}

		public function getFilename() {
			return basename($this->path);
		}

		public function getFilenameWithoutExtension() {

			$pathinfo = pathinfo($this->getPath());

			if (!isset($pathinfo['extension'])) {
				return $this->getFilename();
			}

			return basename($this->path, '.' . $pathinfo['extension']);

		}

		public function delete() {
			$unlinkResult = unlink($this->getPath());
			if (!$unlinkResult) {
				throw new \RuntimeException('Error deleting ' . $this->getPath());
			}
		}
	}
}

namespace Utility {

	class ShellUtility {

		public static function println($string) {
			echo $string . "\n";
		}
	}

	class ArrayUtility {

		public static function getLastArrayIndex($array) {
			return count($array) - 1;
		}
	}
}

namespace Migrate {

	class FileMigration {

		protected $libchartPhpTemplate =
'<?php
	/* Libchart - PHP chart library
	 * Copyright (C) 2005-2011 Jean-Marc Tr�meaux (jm.tremeaux at gmail.com)
	 *
	 * This program is free software: you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation, either version 3 of the License, or
	 * (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 *
	 */

	 die(\'Using libchart.php is not supported any more. Please install the composer package and use the composer autoload.php file. See http://getcomposer.org for more information.\');
?>';

		protected $commonTemplate =
'<?php
	$autoloader = \'../../vendor/autoload.php\';
	if (!file_exists($autoloader)) {
		die($autoloader . \'not found. Please make sure you run "composer install" before running the tests.\');
	}
	require_once $autoloader;
?>';

		public function migrateIncludeFiles() {
			$file = new \Resource\File('demo/common.php', TRUE);
			$file->replaceContent($this->commonTemplate);
			$file = new \Resource\File('test/common.php');
			$file->replaceContent($this->commonTemplate);

			if (!is_dir('libchart/classes/')) {
				$mkdirResult = mkdir('libchart/classes/');
				if (!$mkdirResult) {
					throw new \RuntimeException('Error creating libchart/classes/ directory.');
				}
			}
			$file = new \Resource\File('libchart/classes/libchart.php', TRUE);
			$file->replaceContent($this->libchartPhpTemplate);

			if (is_file('libchart/Libchart/libchart.php')) {
				$file = new \Resource\File('libchart/Libchart/libchart.php');
				$file->delete();
			}
		}

	}

	class ClassMigration {

		protected $migratedClasses = array();

		public function getMigratedClasses() {
			return $this->migratedClasses;
		}

		public function migrateClassesRecursive(\Resource\Directory $directory, $namespaceParts = array()) {

			$classDirectoryReader = new \Resource\DirectoryReader($directory->getPath());
			$namespaceParts[] = ucfirst($directory->getLastPathPart());

			/** @var \Resource\File $file */
			foreach ($classDirectoryReader->getFiles() as $file) {
				$this->initializeClassNamespace($file, $namespaceParts);
			}

			/** @var \Resource\Directory $directory */
			foreach ($classDirectoryReader->getDirectories() as $subDirectory) {
				$this->migrateClassesRecursive($subDirectory, $namespaceParts);
			}

			$directory->renameToUcFirst();
		}

		protected function initializeClassNamespace(\Resource\File $file, $namespaceParts) {

			$fileContent = $file->getContent();

			$matches = array();
			if (preg_match('!/\*\*.*\*/.*(class|interface) ([A-Za-z0-9]+)!s', $fileContent, $matches) !== 1) {
				throw new \RuntimeException('Could not find class definition in file ' . $file->getPath());
			}

			$className = $matches[2];
			if ($className !== $file->getFilenameWithoutExtension()) {
				throw new \RuntimeException(sprintf('The classname %s did not match the current filename %s', $className, $file->getFilename()));
			}

			if (strstr($fileContent, 'namespace') !== FALSE) {
				\Utility\ShellUtility::println(sprintf('Class file %s already seems to have a namespace. Removing...', $file->getPath()));
				$fileContent = preg_replace('![ \t]*namespace [^;]+;.*?([ ]+/\*)!s', '\1', $fileContent);
			}

			$targetNamespace = implode('\\', $namespaceParts);
			\Utility\ShellUtility::println(sprintf('Setting namespace to %s in class file %s.', $targetNamespace, $file->getPath()));

			$classStart = $matches[0];
			$fileParts = explode($classStart, $fileContent);
			$classStart = 'namespace ' . $targetNamespace . ';' . "\n\n    " . $classStart;
			$newFileContent = implode($classStart, $fileParts);
			$file->replaceContent($newFileContent);

			$this->registerMigratedClass($className, $targetNamespace . '\\' . $className);
		}

		protected function registerMigratedClass($oldClassName, $newClassName) {
			$this->migratedClasses[$oldClassName] = $newClassName;
		}
	}

	class CodeMigration {

		protected $classMigration;

		public function __construct(ClassMigration $classMigration) {
			$this->classMigration = $classMigration;

		}

		public function migrateCodeRecursiveley(\Resource\DirectoryReader $directoryReader) {

			/** @var \Resource\File $file */
			foreach ($directoryReader->getFiles() as $file) {
				$fileContent = $file->getContent();
				$fileContent = $this->replaceClassUsage($fileContent);
				$file->replaceContent($fileContent);
			}

			/** @var \Resource\Directory $directory */
			foreach ($directoryReader->getDirectories() as $directory) {
				$this->migrateCodeRecursiveley(new \Resource\DirectoryReader($directory->getPath()));
			}
		}

		protected function replaceClassUsage($content) {

			foreach ($this->classMigration->getMigratedClasses() as $className => $namespacedClassName) {

				$slashedNamespacedClassName = '\\' . $namespacedClassName;
				$doubleSlashedNamespacedClassName = str_replace('\\', '\\\\', $slashedNamespacedClassName);

				$content = str_replace('new ' . $className . '(', 'new ' . $slashedNamespacedClassName . '(', $content);
				$content = str_replace('@param ' . $className . ' ', '@param ' . $slashedNamespacedClassName . ' ', $content);
				$content = str_replace('implements ' . $className, 'implements ' . $slashedNamespacedClassName, $content);
				$content = str_replace('extends ' . $className, 'extends ' . $slashedNamespacedClassName, $content);
				$content = str_replace('instanceof ' . $className, 'instanceof ' . $slashedNamespacedClassName, $content);

				// for use in callbacks
				$content = str_replace('array("' . $className . '"', 'array("' . $doubleSlashedNamespacedClassName . '"', $content);

				// replace the constructors
				$content = str_replace('::' . $className . '(', '::__construct(', $content);
				$content = str_replace('function ' . $className . '(', 'function __construct(', $content);

				// replace old include statements
				$content = preg_replace('!include "(.*)libchart/classes/libchart.php";!', 'include "\1vendor/autoload.php";', $content);
			}
			return $content;
		}

	}
}
