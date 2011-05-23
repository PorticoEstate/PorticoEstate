package no.bimconverter.ifc.v2x3.object;


import static junit.framework.Assert.assertNotNull;

import java.util.List;
import javax.xml.bind.JAXBException;
import jsdai.lang.SdaiException;
import no.bimconverter.ifc.IfcTestMethods;
import no.bimconverter.ifc.jaxb.Attributes;
import no.bimconverter.ifc.jaxb.Decomposition;
import no.bimconverter.ifc.jaxb.PropertyList;
import no.bimconverter.ifc.jaxb.owner.Address;
import org.junit.After;
import static org.junit.Assert.*;
import org.junit.Before;
import org.junit.Test;

public class SiteTest extends IfcTestMethods{

	
	int numberOfIfcElements = 1420;
	int numberOfIfcModels = 0;
	
	
	private Site site;
	
	
	@Before
	public void setUp() {
		super.createTestRepo();
		site = model.getSite();
	}
	@After
	public void tearDown() {
	}
	@Test
	public void outputNewModel() throws SdaiException {
		// Following line outputs IFC representation
		//model.createNewMiniModel();
		
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
		//super.outputXmlToSystemOut(site);
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
