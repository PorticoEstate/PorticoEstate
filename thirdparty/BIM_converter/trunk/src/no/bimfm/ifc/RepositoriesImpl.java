package no.bimfm.ifc;


import java.util.ArrayList;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import no.bimfm.jaxb.rest.RepositoryStatus;

import jsdai.lang.AEntity;
import jsdai.lang.ASdaiRepository;
import jsdai.lang.A_string;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
import jsdai.lang.SdaiRepository;
import jsdai.lang.SdaiSession;

public class RepositoriesImpl extends IfcSdaiRepresentationImpl implements Repositories  {
	private Logger logger = LoggerFactory.getLogger("no.bimfm.ifc.RepositoriesImpl");
	private List<String> repositoryNames= new ArrayList<String>();
	private List<String> repositoryInfo= new ArrayList<String>();
	private RepositoryStatus repositoryStatus = new RepositoryStatus();
	java.util.Properties prop = new java.util.Properties();
	private SdaiSession sdaiSession;
	protected ASdaiRepository repositoryAggregation;
	protected SdaiIterator repositoryIterator;
	
	public RepositoriesImpl() {
		super();
		try {
			this.initRepositoryData();
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RepositoryExceptionUc("Could not initialize repositories", e);
		}
	}
	
	private void initRepositoryData() throws SdaiException {
		super.openSdaiSession();
		extractRepositories();
		
		while(repositoryIterator.next()) {
			SdaiRepository repo = repositoryAggregation.getCurrentMember(repositoryIterator);
			if(!repo.isActive()) {
				repo.openRepository();
			}
			transaction.commit();
			this.repositoryNames.add(repo.getName());
			this.repositoryInfo.add(getRepositoryInfo(repo));
		}
		super.closeSdaiSession();
	}

	protected void extractRepositories() throws SdaiException {
		
		repositoryAggregation = session.getKnownServers();
		logger.debug("Known servers count is: {}",repositoryAggregation.getMemberCount());
		repositoryIterator = repositoryAggregation.createIterator();
	}
	
	@Override
	public RepositoryStatus getRepositoryStatus() {
		this.repositoryStatus.setRepositoryCount(String.valueOf(this.repositoryNames.size()));
		for(String repositoryName : this.repositoryNames) {
			this.repositoryStatus.addRepositoryName(repositoryName);
		}
		for(String repositoryInformation : this.repositoryInfo) {
			this.repositoryStatus.addRepositoryInfo(repositoryInformation);
		}
		return repositoryStatus;
		/*
		repoStatus.append("Number of repositories:"+this.repositoryNames.size()+"\n");
		repoStatus.append("Names of repositories:\n");
		for(String s : this.repositoryNames) {
			repoStatus.append(s + "\n");
		}
		repoStatus.append("Info on repositores:\n");
		for(String s : this.repositoryInfo) {
			repoStatus.append("--\n");
			repoStatus.append(s + "\n");
		}
		return repoStatus.toString();
		*/
	}
	@Override
	public boolean checkIfRepositoryExists(String repositoryName){
		super.openSdaiSession();
		try {
			this.extractRepositories();
			this.repositoryNames = new ArrayList<String>();
			while (repositoryIterator.next()) {
				String currentRepositoryName = repositoryAggregation.getCurrentMember(repositoryIterator).getName();
				if ( currentRepositoryName.equals(repositoryName)) {
					return true;
				}
			}
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RepositoryExceptionUc("Could not check if Repository exists");
		} finally {
			super.closeSdaiSession();
		}
		return false;
	}
	/*
	 * @param SdaiRepository
	 * @return Returns a string of most of the data from the IFC file header
	 */
	private String getRepositoryInfo(SdaiRepository repository) throws SdaiException {
		StringBuilder info = new StringBuilder();
		if(repository.isActive()) {
			
			
			A_string ss = repository.getOrganization();
			for(int i = 1; i <= ss.getMemberCount(); i++) {
				info.append("Organization:"+ss.getByIndex(i)+ "\n");
			}
			ss = repository.getAuthor();
			for(int i = 1; i <= ss.getMemberCount(); i++) {
				info.append("Author:"+ss.getByIndex(i)+ "\n");
			}
			ss =repository.getContextIdentifiers();
			for(int i = 1; i <= ss.getMemberCount(); i++) {
				info.append("Context Identifier:"+ss.getByIndex(i)+ "\n");
			}
			ss =repository.getDescription();
			for(int i = 1; i <= ss.getMemberCount(); i++) {
				info.append("Description:"+ss.getByIndex(i)+ "\n");
			}
			
			info.append("Real name:"+repository.getRealName()+ "\n");
			info.append("Name:"+repository.getName()+ "\n");
			info.append("Location:"+repository.getLocation()+ "\n");
			info.append("Preprocessor:"+ repository.getPreprocessorVersion()+ "\n");
			info.append("Originating system:"+ repository.getOriginatingSystem()+ "\n");
			info.append("Authorization:"+repository.getAuthorization()+ "\n");
		} 
		return info.toString();
	}
	@Override
	public boolean addRepository(String repoName, String fileName) {
		return importRepository(repoName, fileName);
		/*try {
			return importRepository(repoName, fileName);
		} catch (RepositoryExceptionUc e) {
			throw e;
		}*/
	}
	private boolean importRepository(String repoName, String fileName) {
		try {
			if(checkIfRepositoryExists(repoName)) {
				throw new RepositoryExceptionUc("Repository already exists!");
			} else {
				super.openSdaiSession();
				StringBuilder sb = new StringBuilder();
				sb.append("About to import following:\n");
				sb.append("Repository name:\t"+repoName+"\n");
				sb.append("filename:\t "+fileName);
				logger.debug(sb.toString());
				SdaiRepository repo = session.importClearTextEncoding(repoName,fileName, null);
				/*
				if(!repo.isActive()) {
					repo.openRepository();
				}
				*/
				
				transaction.commit();
				/*
				ASdaiModel models = repo.getModels();
				SdaiIterator modelIterator = models.createIterator();
				while(modelIterator.next()) {
					SdaiModel model = models.getCurrentMember(modelIterator);
					//System.out.println("model");
				}
				
				System.out.println("Model count is:"+models.getMemberCount());
				System.out.println("Instance count is:"+models.getInstanceCount());
				*/
				super.closeSdaiSession();
				return true;
			}
		} catch (SdaiException e) {
			super.closeSdaiSession();
			logger.error("Exception thrown while adding repository");
			//e.printStackTrace();
			logger.debug("MSG:"+e.getMessage());
			if(e.getMessage().contains("Exchange structure parsing error") || e.getMessage().contains("Exchange structure scanning error")) {
				logger.error("Throwing invalidifcfileexception");
				logger.error(e.getMessage());
				throw new InvalidIfcFileException("Invalid file", e);
			} else {
				logger.error("Throwing repository exception");
				throw new RepositoryExceptionUc("Error adding repository", e);
			}
			
		} finally {
			//super.closeSdaiSession();
		}
	}
	/*
	public List<EEntity> getModel(String repoName) throws RepositoryException{
		try {
			if(checkIfRepositoryExists(repoName)) {
				sdaiSession =SdaiSession.openSession();
				SdaiTransaction transaction = sdaiSession.startTransactionReadWriteAccess();
				ASdaiRepository repositoryAgg = sdaiSession.getKnownServers();
				SdaiIterator arIter = repositoryAgg.createIterator();
				SdaiRepository myRepository = null;
				while (arIter.next()) {
					SdaiRepository repository = repositoryAgg.getCurrentMember(arIter);
					if ( repository.getName().equals(repoName)) {
						myRepository = repository;
						break;
					}
				}
				if (!myRepository.isActive()) {
					myRepository.openRepository();
				}
				transaction.commit();
				ASdaiModel models = myRepository.getModels();
				//System.out.println("Model count:"+models.getMemberCount());
				//System.out.println("Instance count:"+models.getInstanceCount());
				SdaiModel model = models.getByIndex(1);
				if(model.getMode() == SdaiModel.NO_ACCESS) {
					model.startReadOnlyAccess();
				}
				List<EEntity> modelEntities = getModelEntitiesAsList(model);
				
				return modelEntities;
				
			} else {
				throw new RepositoryException("Repo does not exist!");
				}
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RepositoryException("Error getting model", e);
		} finally {
			closeSdaiSession();
		}
	}
	*/
	
	private List<EEntity> getModelEntitiesAsList(SdaiModel model) throws SdaiException {
		List<EEntity> modelEntities = new ArrayList<EEntity>();
		AEntity entitiesAggregation = model.getInstances();
		SdaiIterator entitiesIterator = entitiesAggregation.createIterator();
		while(entitiesIterator.next()) {
			EEntity entity = entitiesAggregation.getCurrentMemberEntity(entitiesIterator);
			modelEntities.add(entity);
		}
		return modelEntities;
	}
	
	@Override
	public boolean deleteAllRepositories() {
		logger.info("Deleting all repositories");
		super.openSdaiSession();
		try {
			this.extractRepositories();
			while (repositoryIterator.next()) {
				SdaiRepository repository = repositoryAggregation.getCurrentMember(repositoryIterator);
				repository.deleteRepository();
			}
			logger.info("All found repositories have been deleted");
			super.closeSdaiSession();
			return true;
		} catch (SdaiException e) {
			super.closeSdaiSession();
			logger.error("Sdai exception while trying to delete all repositories!");
			e.printStackTrace();
			throw new RepositoryExceptionUc("Error deleting all repositories", e);
		}
	}
	
	@Override
	public boolean deleteRepository(String repositoryName){
		try {
			return findRepositoryAndDelete(repositoryName);
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RepositoryExceptionUc("Error deleting repository", e);
		} 
	}


	private boolean findRepositoryAndDelete(String repositoryName) throws SdaiException {
		super.openSdaiSession();
		this.extractRepositories();
		while (repositoryIterator.next()) {
			SdaiRepository repository = repositoryAggregation.getCurrentMember(repositoryIterator);
			if ( repository.getName().equals(repositoryName)) {
				repository.deleteRepository();
				super.closeSdaiSession();
				return true;
			}
		}
		super.closeSdaiSession();
		throw new RepositoryExceptionUc("Repository Not found");
	}
	
	@Override
	public int getNumberOfRepositories() {
		return this.repositoryNames.size();
	}
	
	@Override
	public List<String> getRepositoryNames() {
		return new ArrayList<String>(repositoryNames);
	}
	
}
