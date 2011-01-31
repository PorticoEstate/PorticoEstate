package no.bimconverter.ifc.jaxb.rest;

import java.util.ArrayList;
import java.util.List;

import javax.xml.bind.annotation.XmlElement;
import javax.xml.bind.annotation.XmlElementWrapper;
import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class RepositoryStatus {
	private String repositoryCount;
	private List<String> RepositoryNames;
	private List<String> RepositoryInfo;
	
	public RepositoryStatus() {
		this.RepositoryNames = new ArrayList<String>();
		this.RepositoryInfo = new ArrayList<String>();
	}
	
	public void addRepositoryName(String name) {
		this.RepositoryNames.add(name);
	}
	
	public void addRepositoryInfo(String info) {
		this.RepositoryInfo.add(info);
	}

	public String getRepositoryCount() {
		return repositoryCount;
	}

	public void setRepositoryCount(String repositoryCount) {
		this.repositoryCount = repositoryCount;
	}
	@XmlElementWrapper(name="names")
	@XmlElement(name="repository")
	public List<String> getRepositoryNames() {
		return RepositoryNames;
	}

	public void setRepositoryNames(List<String> repositoryNames) {
		RepositoryNames = repositoryNames;
	}
	@XmlElementWrapper(name="information")
	@XmlElement(name="repository")
	public List<String> getRepositoryInfo() {
		return RepositoryInfo;
	}

	public void setRepositoryInfo(List<String> repositoryInfo) {
		RepositoryInfo = repositoryInfo;
	}
	

}
