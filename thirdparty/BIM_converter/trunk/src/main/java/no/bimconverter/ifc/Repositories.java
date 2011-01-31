package no.bimconverter.ifc;

import java.util.List;

import no.bimconverter.ifc.jaxb.rest.RepositoryStatus;



public interface Repositories {
	/*
	 * Information on all existing repositories
	 * @return string containing information existing repositories
	 */
	public RepositoryStatus getRepositoryStatus();
	
	/*
	 * If it returns 1, then only the default (and usually empty) system repository exists
	 */
	public int getNumberOfRepositories();
	/*
	 * @return empty list if there are no names
	 */
	public List<String> getRepositoryNames();
	
	public boolean deleteRepository(String repository);
	
	public boolean checkIfRepositoryExists(String repoName);
	/*
	 * @throws RepositoryExceptionUc if it already exists
	 */
	public boolean addRepository(String repoName, String fileName);
	
	public boolean deleteAllRepositories();
}
