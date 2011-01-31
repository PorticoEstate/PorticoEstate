package no.bimfm.ifc.v2x3.object;


import static junit.framework.Assert.assertNotNull;

import java.io.FileNotFoundException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import jsdai.lang.EEntity;
import jsdai.lang.SdaiException;
import jsdai.lang.SdaiModel;
import jsdai.lang.SdaiRepository;
import jsdai.lang.SdaiSession;
import jsdai.lang.SdaiTransaction;

import no.bimfm.ifc.Repositories;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.v2x3.IfcModelImpl;
import no.bimfm.ifc.v2x3.owner.Address;
import no.bimfm.jaxb.Attributes;
import no.bimfm.jaxb.ClassificationItem;
import no.bimfm.jaxb.Decomposition;
import no.bimfm.jaxb.PropertyList;

import org.junit.After;
import static org.junit.Assert.*;
import org.junit.Before;
import org.junit.Test;

public class SiteTest {

	//String testingRepository = "ModelTestRepository";
	String testingRepository = "FMHandoverRepository";
	String nonExistingRepository = "dummmmmyRepoThatDoesNotExist";
	//String testIfcFileName = "sample.ifc";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	String ifcFilename = null;
	private IfcModelImpl model;
	private Site site;
	Repositories repo = null;
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		site = model.getSite();
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	@Test
	public void outputNewModel() throws SdaiException {
		model.createNewMiniModel();
		/*
		SdaiSession session2 = SdaiSession.openSession();
		SdaiTransaction transaction = session2.startTransactionReadWriteAccess(); 
		SdaiRepository repo2 = session2.createRepository("", null);
		repo2.openRepository();
		List<EEntity> listy = model.getObjectsDefinitions();
		
		SdaiModel modelNew = repo2.createSdaiModel("Model1", jsdai.SIfc2x3.SIfc2x3.class);
		modelNew.startReadWriteAccess();
		modelNew.copyInstance(listy.get(0));
		transaction.endTransactionAccessAbort();
		repo2.closeRepository();
		repo2.deleteRepository();

		System.out.println();
		System.out.println("Done");
		session2.closeSession();
		*/
	}
	@Test
	public void testCreateAndIntializeSiteObject() throws JAXBException {
		
		
		 
		JAXBContext jc = JAXBContext.newInstance(Site.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( site, System.out );

		 
		assertNotNull(site);
	}
	@Test
	public void testSiteAttributes() {
		Attributes attributes = site.getAttributes();
		
		assertEquals("28hfXoRX9EMhvGvGhmaaad", attributes.getGuid());
		assertEquals("01.1", attributes.getName());
		assertEquals("Site object for testing Basic FM Handover", attributes.getDescription());
		assertEquals("FM Test Site", attributes.getLongName());
		assertEquals("50;58;33;110400", attributes.getLatitude());
		assertEquals("11;20;32;161199", attributes.getLongtitude());
		assertEquals("249.0", attributes.getElevation());
	}
	@Test
	public void testSiteAddress() {
		Address attributes = site.getAddress();
		assertEquals("Albrecht-Dürer-Strasse 18", attributes.getAddressLines());
		assertEquals("Weimar", attributes.getTown());
		assertEquals(null, attributes.getRegion());
		assertEquals("99423", attributes.getPostalCode());
	}
	// Trivial
	@Test
	public void testClassificationIsEmpty() {
		//List<ClassificationItem> classification = site.getClassification();
		//assertNull(classification);
	}
	
	@Test
	public  void testSpatialComposition() {
		//Map<String, ArrayList<String>> spatialComposition = site.getSpatialDecomposition();
		//Map<String, String[]> spatialComposition = site.getSpatialDecomposition();
		Decomposition spatialComposition = site.getSpatialDecomposition();
		//String[] projectContainingSite = spatialComposition.get(Site.SpatialDecomposition.PROJECT.key);
		assertNotNull(spatialComposition.getProject());
		assertEquals("3KFKb0sfrDJwSHalGIQFZT", spatialComposition.getProject());
		//String[] buildingContainedInSite = spatialComposition.get(Site.SpatialDecomposition.BUILDING.key);
		assertEquals(spatialComposition.getBuildings().size(), 1);
		assertEquals("28hfXoRX9EMhvGvGhmaaae", spatialComposition.getBuildings().get(0));	
	}
	@Test
	public void testProperties() {
		List<PropertyList> siteProperties = site.getProperties();
		assertEquals(null, siteProperties);
	}

}
