package no.bimfm.ifc;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.util.Properties;

import jsdai.lang.SdaiException;
import jsdai.lang.SdaiSession;
import jsdai.lang.SdaiTransaction;

public class IfcSdaiRepresentationImpl implements IfcSdaiRepresentation{
	protected SdaiSession session = null;
	protected SdaiTransaction transaction =null;
	protected Properties properties = new java.util.Properties();
	
	public IfcSdaiRepresentationImpl() {
		this(propertiesFileName);
	}
	public IfcSdaiRepresentationImpl(String propertiesFileNameArg) {
		InputStream inStream;
		try {
			inStream =  Thread.currentThread().getContextClassLoader().getResourceAsStream(propertiesFileNameArg);
			if(inStream == null) {
				throw new FileNotFoundException();
			}
			properties.load(inStream);
			SdaiSession.setSessionProperties(properties);
		} catch (FileNotFoundException e1) {
			e1.printStackTrace();
			throw new IfcSdaiException("File not found!", e1);
		} catch (IOException e) {
			e.printStackTrace();
			throw new IfcSdaiException("IO error!", e);
		} catch (SdaiException e) {
			e.printStackTrace();
			throw new IfcSdaiException("Sdai error!", e);
		}
	}
	
	protected void openSdaiSession() {
		try {
			//session=SdaiSession.getSession();
			//session.closeSession();
			session=SdaiSession.openSession();
			//session = SdaiSession.getSession();
			transaction = session.startTransactionReadWriteAccess();
			
			
		} catch (SdaiException e) {
			
			e.printStackTrace();
		}
	}
	protected void closeSdaiSession() {
		try {
			if(session != null)
				session.closeSession();
		} catch (SdaiException e) {
			e.printStackTrace();
		}
	}
	
	
}
