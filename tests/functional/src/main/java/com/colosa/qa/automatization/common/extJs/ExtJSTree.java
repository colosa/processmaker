package com.colosa.qa.automatization.common.extJs;

import java.util.List;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.By;

public class ExtJSTree{

	private WebDriver driver;
	private WebElement tree;
	private int timeout;
	private WebElement currentNode;
	private WebElement root;

	public ExtJSTree(WebElement tree, WebDriver driver, int timeout){
		this.driver = driver;
		this.tree = tree;
		this.timeout = timeout;
		this.root = tree.findElement(By.xpath("div[2]/div/ul/li"));
		this.currentNode = this.root;
	}

	public void gotoRoot(){
		this.root.findElement(By.xpath("div/a/span")).click();
		this.currentNode = this.root;
	}

	public void refresh(){
		this.tree.findElement(By.xpath("div[1]/div[1]")).click();
		//talvez esperar a que se termine de cargar
	}

	public WebElement gotoNode(String path){
		String itemToSearch = null;
		List<WebElement> childs = null;
		WebElement elm = (path.charAt(0)=='/')?this.root:this.currentNode;

		path = (path.charAt(path.length()-1)=='/')?path.substring(0, path.length()-1):path;
		path = (path.charAt(0)=='/')?path.substring(1):path;
		if(path.indexOf('/')>0)
		{
			itemToSearch = path.substring(0, path.indexOf('/'));
			path = path.substring(itemToSearch.length()+1);
		}
		else
			itemToSearch = path;

		childs = elm.findElements(By.xpath("ul/li"));

		for(WebElement child:childs){
			if(child.findElement(By.xpath("div/a/span")).getText().equals(itemToSearch)){
				elm = child;
				this.currentNode = elm;
				break;
			}
		}

		elm.findElement(By.xpath("div/a/span")).click();

		if(path.equals(itemToSearch))
			return elm;
		else
			return gotoNode(path);
	}

	public WebElement getRootNode(){
		return this.root;
	}

	public WebElement getCurrentNode(){
		return this.currentNode;
	}
}
