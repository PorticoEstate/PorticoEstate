package no.bimfm.ifc.v2x3;


import java.util.List;

import javax.xml.bind.JAXBContext;
import javax.xml.bind.JAXBException;
import javax.xml.bind.Marshaller;

import static junit.framework.Assert.assertNotNull;
import static org.junit.Assert.*;

import no.bimfm.ifc.Repositories;
import no.bimfm.ifc.RepositoriesImpl;
import no.bimfm.ifc.v2x3.object.BuildingStorey;
import no.bimfm.ifc.v2x3.object.Project;

import org.junit.After;
import org.junit.Before;
import org.junit.Test;

public class ModelInformationTest {

	String testingRepository = "FMHandoverRepository";
	String testIfcFileName = "20091007_Test_BasicFM-HandOver_01_valid.ifc";
	
	private IfcModelImpl model;
	private ModelInformation modelInformation;

	Repositories repo = null;
	
	@Before
	public void setUp() {
		model = new IfcModelImpl(testingRepository);
		String ifcFilename = (Thread.currentThread().getContextClassLoader().getResource(testIfcFileName)).toString();
		ifcFilename = ifcFilename.replace("file:/", "");
		repo = new RepositoriesImpl();
		repo.addRepository(testingRepository, ifcFilename);
		this.modelInformation = model.getExchangeFileProperties();
	}
	@After
	public void tearDown() {
		repo.deleteRepository(testingRepository);
	}
	@Test
	public void testDisplayObject() throws JAXBException {
		assertNotNull(this.modelInformation);
		JAXBContext jc = JAXBContext.newInstance(ModelInformation.class);
		Marshaller m = jc.createMarshaller();
		 m.setProperty(Marshaller.JAXB_FORMATTED_OUTPUT, true);
		m.marshal( this.modelInformation, System.out );
		
	}
	@Test
	public void testValues() {
		assertEquals("Thomas Liebich", this.modelInformation.getAuthor());
		assertEquals("reference file created for the Basic FM Handover View", this.modelInformation.getAuthorization());
		assertNotNull(this.modelInformation.getChangeDate());
		assertEquals("ViewDefinition [CoordinationView, FMHandOverView]", this.modelInformation.getDescription());
		assertEquals("IFC2X3", this.modelInformation.getNativeSchema());
		assertEquals("AEC3", this.modelInformation.getOrganization());
		assertEquals("IFC text editor", this.modelInformation.getOriginatingSystem());
		assertEquals("IFC text editor", this.modelInformation.getPreProcessor());
	}

}
