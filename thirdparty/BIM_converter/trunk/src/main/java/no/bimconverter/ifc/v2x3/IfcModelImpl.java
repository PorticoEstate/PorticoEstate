package no.bimconverter.ifc.v2x3;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.List;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

import jsdai.SIfc2x3.EIfcobjectdefinition;
import jsdai.lang.AEntity;
import jsdai.lang.ASdaiRepository;
import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiIterator;
import jsdai.lang.SdaiModel;
import jsdai.lang.SdaiRepository;
import no.bimconverter.ifc.IfcModel;
import no.bimconverter.ifc.IfcSdaiException;
import no.bimconverter.ifc.RepositoriesImpl;
import no.bimconverter.ifc.loader.CommonLoader;
import no.bimconverter.ifc.v2x3.object.Building;
import no.bimconverter.ifc.v2x3.object.BuildingStorey;
import no.bimconverter.ifc.v2x3.object.CommonObject;
import no.bimconverter.ifc.v2x3.object.FacilityManagementEntity;
import no.bimconverter.ifc.v2x3.object.Project;
import no.bimconverter.ifc.v2x3.object.Site;
import no.bimconverter.ifc.v2x3.object.Space;
import no.bimconverter.ifc.v2x3.object.Zone;
import no.bimconverter.ifc.v2x3.object.element.BuildingServiceElement;
import no.bimconverter.ifc.v2x3.object.element.Covering;
import no.bimconverter.ifc.v2x3.object.element.Door;
import no.bimconverter.ifc.v2x3.object.element.Furnishing;
import no.bimconverter.ifc.v2x3.object.element.Window;




public class IfcModelImpl extends RepositoriesImpl implements IfcModel{
	private Logger logger = LoggerFactory.getLogger("no.bimconverter.ifc.v2x3.IfcModelImpl");
	private String ifcRepositoryName;
	private int size = 0;
	protected List<EEntity> objectList = new ArrayList<EEntity>();
	protected SdaiModel model;
	private SdaiRepository repository;
	private Project project = new Project();
	private Site site = new Site();
	public IfcModelImpl() {
		super();
	}
	
	public IfcModelImpl(String ifcRepositoryName) {
		this();
		this.setIfcRepositoryName(ifcRepositoryName);
	}
	
	public void setIfcRepositoryName(String ifcRepositoryName) {
		this.ifcRepositoryName = ifcRepositoryName;
	}
	public String getIfcRepositoryName() {
		return ifcRepositoryName;
	}

	public int size() {
		super.openSdaiSession();
		try {
			super.extractRepositories();
			SdaiRepository repository = getCurrentRepository(repositoryAggregation, repositoryIterator);
			model = repository.getModels().getByIndex(1); // making an assumption of a single model
			makeModelReadable();
			size = model.getInstanceCount();
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new IfcSdaiException("Error getting size", e);
		} finally {
			super.closeSdaiSession();
		}
		
		return size;
	}
	private SdaiRepository getCurrentRepository(ASdaiRepository repositoryAggr, SdaiIterator repositoryIterator) {
		try {
			while(repositoryIterator.next()) {
				SdaiRepository repository = repositoryAggregation.getCurrentMember(repositoryIterator);
				if(repository.getName().equals(this.ifcRepositoryName)) {
					
					checkAndOpenRepository(repository);
					return repository;
				}
			}
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new IfcSdaiException("Error getting current repository", e);
		}
		throw new IfcSdaiException("Could not find the repository");
	}

	protected void makeModelReadable() throws SdaiException {
		if(model.getMode() == SdaiModel.NO_ACCESS) {
			model.startReadOnlyAccess();
		}
	}

	protected void checkAndOpenRepository(SdaiRepository repo) throws SdaiException {
		if(!repo.isActive()) {
			repo.openRepository();
		}
	}

	public List<EEntity> getObjectsDefinitions() {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			this.objectList = getInstancesOfType(EIfcobjectdefinition.class);
			return objectList;
		} catch (SdaiException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} finally {
			super.closeSdaiSession();
		}
		
		return null;
	}
	// Unit test for this method is in SiteTest
	public void createNewMiniModel() {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			
			SdaiRepository repo2 = session.createRepository("asdf", null);
			repo2.openRepository();
			
			
			SdaiModel modelNew = repo2.createSdaiModel("Model1", jsdai.SIfc2x3.SIfc2x3.class);
			modelNew.setOptimized(true);
			modelNew.startReadWriteAccess();
			
			
			
			Project.getIfcRepresentation(model, modelNew);
			
			
			ByteArrayOutputStream os = new ByteArrayOutputStream();
			repo2.exportClearTextEncoding(os);
			try {
				String out = new String(os.toByteArray(), "UTF-8");
				os.close();
				System.out.println(out);
			} catch (UnsupportedEncodingException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			
			
			
			
			
			transaction.endTransactionAccessAbort();
			
			repo2.closeRepository();
			repo2.deleteRepository();

			
			
		} catch (SdaiException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} finally {
			super.closeSdaiSession();
		}
		
		
	}

	protected List<EEntity> getInstancesOfType(Class classType) throws SdaiException {
		objectList.clear();
		AEntity entities = model.getInstances();
		SdaiIterator entitiesIter = entities.createIterator();
		while(entitiesIter.next()) {
			EEntity entity =  entities.getCurrentMemberEntity(entitiesIter);
			if(entity.isKindOf(classType)) {
				objectList.add(entity);
			}
		}
		return objectList;
	}

	protected void makeModelAvailable() throws SdaiException {
		super.extractRepositories();
		repository = getCurrentRepository(repositoryAggregation, repositoryIterator);
		model = repository.getModels().getByIndex(1);
		makeModelReadable();
	}

	
	public ModelInformation getExchangeFileProperties() {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			
			return (new ModelInformation().load(model));
			
		} catch (SdaiException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} finally {
			super.closeSdaiSession();
		}
		return null;
		
	}
	
	public List<? extends CommonObject> getFacilityManagementEntity(FacilityManagementEntity facilityManagementEntity) {
		super.openSdaiSession();
		try {
			makeModelAvailable();
			List<CommonObject> output = (List<CommonObject>) (new CommonLoader()).load(model, facilityManagementEntity);
			return output;
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new RuntimeException("Error loading entity:"+facilityManagementEntity);
		} finally {
			super.closeSdaiSession();
		}
	}
	
	public SdaiModel getModel() {
		return model;
	}

	public void setModel(SdaiModel model) {
		this.model = model;
	}

	

}

