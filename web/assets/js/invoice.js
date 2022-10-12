var Invoice = function(taxpayer, customer, publicKey) {
	var items = Array()
	var numeration, serie
	var typeCode, orderReference

	var paymentTerms = Array()

	this.ublVersion = "2.1"
	this.customizationId = "2.0"

	var xmlDocument

	/**
	 * Format serie and number: F000-00000001
	 */
	this.getId = function() {
		if(serie == undefined || numeration == undefined) {
			throw new Error("Serie o número incompletos.")
		}
		return serie + '-' + String(numeration).padStart(8, '0')
	}

	this.addPaymentTerm = function(paymentTerm) {
		paymentTerms.push(paymentTerm)
	}

	this.setUblVersion = function(ublVersion) {
		this.ublVersion = ublVersion
	}

	this.setSerie = function(_serie) {
		if(_serie.length != 4) {
			throw new Error("Serie inconsistente")
		}
		serie = _serie
	}

	this.getSerie = function() {
		return serie
	}

	this.setNumeration = function(number) {
		if(number > 0x5F5E0FF) {
			throw new Error("Numeración supera el límite.")
		}
		numeration = number
	}

	this.getNumeration = function() {
		return numeration
	}

	this.setId = function(serie, numeration) {
		this.setSerie(serie)
		this.setNumeration(numeration)
	}

	this.setTypeCode = function(code) {
		typeCode = code
	}

	this.addItem = function(item) {
		items.push(item)
	}

	this.getXml = function() {
		if(xmlDocument == undefined) {
			throw new Error("XML no creado.")
		}
		return xmlDocument
	}

	this.setOrderReference = function(reference) {
		orderReference = reference
	}

	this.getTotalAmount = function(withFormat = false) {
		return withFormat ? totalAmount.toFixed(2) : totalAmount
	}

	this.getEncryptedTotalAmount = function() {
		return publicKey.encrypt(totalAmount)
	}

	this.toXml = function() {
		//We create elements using this: xmlDocument.createElement
		xmlDocument = document.implementation.createDocument("urn:oasis:names:specification:ubl:schema:xsd:Invoice-2", "Invoice")
		xmlDocument.documentElement.setAttribute("xmlns:cac", namespaces.cac)
		xmlDocument.documentElement.setAttribute("xmlns:cbc", namespaces.cbc)
		xmlDocument.documentElement.setAttribute("xmlns:ds", namespaces.ds)
		xmlDocument.documentElement.setAttribute("xmlns:ext", namespaces.ext)

		const extUblExtensions = xmlDocument.createElementNS(namespaces.ext, "ext:UBLExtensions")
		xmlDocument.documentElement.appendChild(extUblExtensions)

		const extUblExtension = xmlDocument.createElementNS(namespaces.ext, "ext:UBLExtension")
		extUblExtensions.appendChild(extUblExtension)

		const extExtensionContent = xmlDocument.createElementNS(namespaces.ext, "ext:ExtensionContent")
		extUblExtension.appendChild(extExtensionContent)

		const cbcUblVersionId = xmlDocument.createElementNS(namespaces.cbc, "cbc:UBLVersionID")
		cbcUblVersionId.appendChild( document.createTextNode(this.ublVersion) )
		xmlDocument.documentElement.appendChild(cbcUblVersionId)

		const cbcCustomizationId = xmlDocument.createElementNS(namespaces.cbc, "cbc:CustomizationID")
		cbcCustomizationId.appendChild( document.createTextNode(this.customizationId) )
		xmlDocument.documentElement.appendChild(cbcCustomizationId)

		const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
		cbcId.appendChild( document.createTextNode(this.getId()) )
		xmlDocument.documentElement.appendChild(cbcId)

		const cbcIssueDate = xmlDocument.createElementNS(namespaces.cbc, "cbc:IssueDate")
		cbcIssueDate.appendChild( document.createTextNode("2022-09-30") )
		xmlDocument.documentElement.appendChild(cbcIssueDate)

		const cbcInvoiceTypeCode = xmlDocument.createElementNS(namespaces.cbc, "cbc:InvoiceTypeCode")
		cbcInvoiceTypeCode.setAttribute("listID", "0101")
		cbcInvoiceTypeCode.appendChild( document.createTextNode(typeCode) )
		xmlDocument.documentElement.appendChild(cbcInvoiceTypeCode)

		const cbcNote = xmlDocument.createElementNS(namespaces.cbc, "cbc:Note")
		cbcNote.setAttribute("languageLocaleID", "1000")
		cbcNote.appendChild( document.createTextNode("<![CDATA[DIECISÉIS CON 99 / 100 SOLES]]>") )
		xmlDocument.documentElement.appendChild(cbcNote)

		const cbcDocumentCurrencyCode = xmlDocument.createElementNS(namespaces.cbc, "cbc:DocumentCurrencyCode")
		cbcDocumentCurrencyCode.appendChild( document.createTextNode("PEN") )
		xmlDocument.documentElement.appendChild(cbcDocumentCurrencyCode)

		if(orderReference) {
			const cacOrderReference = xmlDocument.createElementNS(namespaces.cac, "cac:OrderReference")
			xmlDocument.documentElement.appendChild(cacOrderReference)

			const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
			cbcId.appendChild( document.createTextNode(orderReference) )
			cacOrderReference.appendChild(cbcId)
		}

		{ //Signer data
			const cacSignature = xmlDocument.createElementNS(namespaces.cbc, "cac:Signature")
			xmlDocument.documentElement.appendChild(cacSignature)

			const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
			cbcId.appendChild( document.createTextNode(taxpayer.getIdentification().getNumber()) )
			cacSignature.appendChild(cbcId)

			{
				const cacSignatoreParty = xmlDocument.createElementNS(namespaces.cac, "cac:SignatoreParty")
				cacSignature.appendChild(cacSignatoreParty)

				const cacPartyIdentification = xmlDocument.createElementNS(namespaces.cac, "cac:PartyIdentification")
				cacSignatoreParty.appendChild(cacPartyIdentification)

				const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
				cbcId.appendChild( document.createTextNode(taxpayer.getIdentification().getNumber()) )
				cacPartyIdentification.appendChild(cbcId)

				const cacPartyName = xmlDocument.createElementNS(namespaces.cac, "cac:PartyName")
				cacSignatoreParty.appendChild(cacPartyName)

				const cbcName = xmlDocument.createElementNS(namespaces.cbc, "cbc:Name")
				cbcName.appendChild( document.createTextNode(taxpayer.getName(true)) )
				cacPartyName.appendChild(cbcName)
			}
		}
		{ //Supplier (current taxpayer)
			const cacAccountingSupplierParty = xmlDocument.createElementNS(namespaces.cbc, "cac:AccountingSupplierParty")
			xmlDocument.documentElement.appendChild(cacAccountingSupplierParty)

			const cacParty = xmlDocument.createElementNS(namespaces.cac, "cac:Party")
			cacAccountingSupplierParty.appendChild(cacParty)

			const cacPartyIdentification = xmlDocument.createElementNS(namespaces.cbc, "cac:PartyIdentification")
			cacParty.appendChild(cacPartyIdentification)

			const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
			cbcId.setAttribute("schemeID", taxpayer.getIdentification().getType())
			cbcId.setAttribute("schemeName", "PE:SUNAT")
			cbcId.setAttribute("schemeURI", "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06")
			cbcId.appendChild( document.createTextNode(taxpayer.getIdentification().getNumber()) )
			cacPartyIdentification.appendChild(cbcId)

			const cacPartyName = xmlDocument.createElementNS(namespaces.cac, "cac:PartyName")
			cacParty.appendChild(cacPartyName)

			const cbcName = xmlDocument.createElementNS(namespaces.cbc, "cbc:Name")
			cbcName.appendChild( document.createTextNode(taxpayer.getTradeName(true)) )
			cacPartyName.appendChild(cbcName)

			const cacPartyLegalEntity = xmlDocument.createElementNS(namespaces.cac, "cac:PartyLegalEntity")
			cacParty.appendChild(cacPartyLegalEntity)

			const cbcRegistrationName = xmlDocument.createElementNS(namespaces.cbc, "cbc:RegistrationName")
			cbcRegistrationName.appendChild( document.createTextNode(taxpayer.getName(true)) )
			cacPartyLegalEntity.appendChild(cbcRegistrationName)

			const cacRegistrationAddress = xmlDocument.createElementNS(namespaces.cac, "cac:RegistrationAddress")
			cacPartyLegalEntity.appendChild(cacRegistrationAddress)
			{
				const metaAddress = taxpayer.getMetaAddress()

				const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
				cbcId.appendChild( document.createTextNode(metaAddress[1]) )
				cacRegistrationAddress.appendChild(cbcId)

				const cbcAddressTypeCode = xmlDocument.createElementNS(namespaces.cbc, "cbc:AddressTypeCode")
				cbcAddressTypeCode.appendChild( document.createTextNode(metaAddress[2]) )
				cacRegistrationAddress.appendChild(cbcAddressTypeCode)

				const cbcCitySubdivisionName = xmlDocument.createElementNS(namespaces.cbc, "cbc:CitySubdivisionName")
				cbcCitySubdivisionName.appendChild( document.createTextNode(metaAddress[3]) )
				cacRegistrationAddress.appendChild(cbcCitySubdivisionName)

				const cbcCityName = xmlDocument.createElementNS(namespaces.cbc, "cbc:CityName")
				cbcCityName.appendChild( document.createTextNode(metaAddress[4]) )
				cacRegistrationAddress.appendChild(cbcCityName)

				const cbcCountrySubentity = xmlDocument.createElementNS(namespaces.cbc, "cbc:CountrySubentity")
				cbcCountrySubentity.appendChild( document.createTextNode(metaAddress[5]) )
				cacRegistrationAddress.appendChild(cbcCountrySubentity)

				const cbcDistrict = xmlDocument.createElementNS(namespaces.cbc, "cbc:District")
				cbcDistrict.appendChild( document.createTextNode(metaAddress[6]) )
				cacRegistrationAddress.appendChild(cbcDistrict)

				const cacCountry = xmlDocument.createElementNS(namespaces.cac, "cac:Country")
				cacRegistrationAddress.appendChild(cacCountry)

				const cbcIdentificationCode = xmlDocument.createElementNS(namespaces.cbc, "cbc:IdentificationCode")
				cbcIdentificationCode.appendChild( document.createTextNode(metaAddress[0]) )
				cacCountry.appendChild(cbcIdentificationCode)

				const cacAddressLine = xmlDocument.createElementNS(namespaces.cac, "cac:AddressLine")
				cacRegistrationAddress.appendChild(cacAddressLine)

				const cbcLine = xmlDocument.createElementNS(namespaces.cbc, "cbc:Line")
				cbcLine.appendChild( document.createTextNode(taxpayer.getAddress(true)) )
				cacAddressLine.appendChild(cbcLine)
			}
		}
		{ //Customer
			const cacAccountingCustomerParty = xmlDocument.createElementNS(namespaces.cac, "cac:AccountingCustomerParty")
			xmlDocument.documentElement.appendChild(cacAccountingCustomerParty)

			const cacParty = xmlDocument.createElementNS(namespaces.cac, "cac:Party")
			cacAccountingCustomerParty.appendChild(cacParty)

			const cacPartyIdentification = xmlDocument.createElementNS(namespaces.cac, "cac:PartyIdentification")
			cacParty.appendChild(cacPartyIdentification)

			const cbcId = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
			cbcId.setAttribute("schemeID", customer.getIdentification().getType())
			cbcId.setAttribute("schemeName", "PE:SUNAT")
			cbcId.setAttribute("schemeURI", "urn:pe:gob:sunat:cpe:see:gem:catalogos:catalogo06")
			cbcId.appendChild( document.createTextNode(customer.getIdentification().getNumber()) )
			cacPartyIdentification.appendChild(cbcId)

			const cacPartyLegalEntity = xmlDocument.createElementNS(namespaces.cac, "cac:PartyLegalEntity")
			cacParty.appendChild(cacPartyLegalEntity)

			const cbcRegistrationName = xmlDocument.createElementNS(namespaces.cbc, "cbc:RegistrationName")
			cbcRegistrationName.appendChild( document.createTextNode(customer.getName(true)) )
			cacPartyLegalEntity.appendChild(cbcRegistrationName)

			const cacRegistrationAddress = xmlDocument.createElementNS(namespaces.cac, "cac:RegistrationAddress")
			cacPartyLegalEntity.appendChild(cacRegistrationAddress)

			const cacAddressLine = xmlDocument.createElementNS(namespaces.cac, "cac:AddressLine")
			cacRegistrationAddress.appendChild(cacAddressLine)

			const cbcLine = xmlDocument.createElementNS(namespaces.cbc, "cbc:Line")
			cbcLine.appendChild( document.createTextNode(customer.getAddress(true)) )
			cacRegistrationAddress.appendChild(cbcLine)
		}

		{ //Taxes
			const cacTaxTotal = xmlDocument.createElementNS(namespaces.cac, "cac:TaxTotal")
			xmlDocument.documentElement.appendChild(cacTaxTotal)
			{
				const cbcTaxAmount = xmlDocument.createElementNS(namespaces.cbc, "cbc:TaxAmount")
				cbcTaxAmount.setAttribute("currencyID", "PEN")
				cbcTaxAmount.appendChild( document.createTextNode("") )
				cacTaxTotal.appendChild(cbcTaxAmount)
			}
		}

		for(const item in items) { //Items
			const cacInvoiceLine = xmlDocument.createElementNS(namespaces.cac, "cac:InvoiceLine")
			xmlDocument.documentElement.appendChild(cacInvoiceLine)

			const cbcID = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
			cbcID.appendChild( document.createTextNode(parseInt(item) + 1) )
			cacInvoiceLine.appendChild(cbcID)

			const cbcInvoicedQuantity = xmlDocument.createElementNS(namespaces.cbc, "cbc:InvoicedQuantity")
			cbcInvoicedQuantity.setAttribute("unitCode", items[item].getUnitCode())
			cbcInvoicedQuantity.setAttribute("unitCodeListID", "UN/ECE rec 20")
			cbcInvoicedQuantity.setAttribute("unitCodeListAgencyName", "United Nations Economic Commission for Europe")
			cbcInvoicedQuantity.appendChild( document.createTextNode(items[item].getQuantity()) )
			cacInvoiceLine.appendChild(cbcInvoicedQuantity)

			const cbcLineExtensionAmount = xmlDocument.createElementNS(namespaces.cbc, "cbc:LineExtensionAmount")
			cbcLineExtensionAmount.setAttribute("currencyID", items[item].getCurrencyId())
			cbcLineExtensionAmount.appendChild( document.createTextNode(items[item].getLineExtensionAmount(true)) )
			cacInvoiceLine.appendChild(cbcLineExtensionAmount)

			{ //PricingReference
				const cacPricingReference = xmlDocument.createElementNS(namespaces.cac, "cac:PricingReference")
				cacInvoiceLine.appendChild(cacPricingReference)

				const cacAlternativeConditionPrice = xmlDocument.createElementNS(namespaces.cac, "cac:AlternativeConditionPrice")
				cacPricingReference.appendChild(cacAlternativeConditionPrice)

				const cbcPriceAmount = xmlDocument.createElementNS(namespaces.cac, "cbc:PriceAmount")
				cbcPriceAmount.setAttribute("currencyID", items[item].getCurrencyId())
				cbcPriceAmount.appendChild( document.createTextNode( items[item].getPricingReferenceAmount(true) ) )
				cacAlternativeConditionPrice.appendChild(cbcPriceAmount)

				const cbcPriceTypeCode = xmlDocument.createElementNS(namespaces.cac, "cbc:PriceTypeCode")
				cbcPriceTypeCode.appendChild(document.createTextNode(items[item].getPriceTypeCode()))
			}

			{ //TaxTotal
				const cacTaxTotal = xmlDocument.createElementNS(namespaces.cac, "cac:TaxTotal")
				cacInvoiceLine.appendChild(cacTaxTotal)

				const cbcTaxAmount = xmlDocument.createElementNS(namespaces.cbc, "cbc:TaxAmout")
				cbcTaxAmount.setAttribute("currencyID", items[item].getCurrencyId())
				cbcTaxAmount.appendChild( document.createTextNode( items[item].getTaxTotalAmount(true) ) )
				cacTaxTotal.appendChild(cbcTaxAmount)

				if( items[item].getIscAmount() > 0 ) { //ISC
					const cacTaxSubtotal = xmlDocument.createElementNS(namespaces.cac, "cac:TaxSubtotal")
					cacTaxTotal.appendChild(cacTaxSubtotal)

					const cbcTaxableAmount = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxableAmount")
					cbcTaxableAmount.setAttribute("currencyID", items[item].getCurrencyId())
					cbcTaxableAmount.appendChild( document.createTextNode( items[item].getLineExtensionAmount(true) ) )
					cacTaxSubtotal.appendChild(cbcTaxableAmount)

					const cbcTaxAmount = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxAmount")
					cbcTaxAmount.setAttribute("currencyID", items[item].getCurrencyId())
					cbcTaxAmount.appendChild( document.createTextNode( items[item].getIscAmount(true) ) )
					cacTaxSubtotal.appendChild(cbcTaxAmount)

					const cacTaxCategory = xmlDocument.createElementNS(namespaces.cac, "cac:TaxCategory")
					cacTaxSubtotal.appendChild(cacTaxCategory)
					{
						const cbcPercent = xmlDocument.createElementNS(namespaces.cac, "cbc:Percent")
						cbcPercent.appendChild( document.createTextNode( items[item].getIscPercentage() ) )
						cacTaxCategory.appendChild(cbcPercent)

						const cbcTierRange = xmlDocument.createElementNS(namespaces.cac, "cbc:TierRange")
						cbcTierRange.appendChild( document.createTextNode( "01" ) )
						cacTaxCategory.appendChild(cbcTierRange)

						const cacTaxScheme = xmlDocument.createElementNS(namespaces.cac, "cac:TaxScheme")
						cacTaxCategory.appendChild(cacTaxScheme)
						{
							const cbcID = xmlDocument.createElementNS(namespaces.cac, "cbc:ID")
							cbcID.appendChild( document.createTextNode( "2000" ) )
							cacTaxScheme.appendChild(cbcID)

							const cbcName = xmlDocument.createElementNS(namespaces.cac, "cbc:Name")
							cbcName.appendChild( document.createTextNode( "ISC" ) )
							cacTaxScheme.appendChild(cbcName)

							const cbcTaxTypeCode = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxTypeCode")
							cbcTaxTypeCode.appendChild( document.createTextNode( "EXC" ) )
							cacTaxScheme.appendChild(cbcTaxTypeCode)
						}
					}
				}
				if( items[item].getIgvAmount() > 0 ) { //IGV
					const cacTaxSubtotal = xmlDocument.createElementNS(namespaces.cac, "cac:TaxSubtotal")
					cacTaxTotal.appendChild(cacTaxSubtotal)

					const cbcTaxableAmount = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxableAmount")
					cbcTaxableAmount.setAttribute("currencyID", items[item].getCurrencyId())
					cbcTaxableAmount.appendChild( document.createTextNode( items[item].getTaxableIgvAmount(true) ) )
					cacTaxSubtotal.appendChild(cbcTaxableAmount)

					const cbcTaxAmount = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxAmount")
					cbcTaxAmount.setAttribute("currencyID", items[item].getCurrencyId())
					cbcTaxAmount.appendChild( document.createTextNode( items[item].getIgvAmount(true) ) )
					cacTaxSubtotal.appendChild(cbcTaxAmount)

					const cacTaxCategory = xmlDocument.createElementNS(namespaces.cac, "cac:TaxCategory")
					cacTaxSubtotal.appendChild(cacTaxCategory)
					{
						const cbcPercent = xmlDocument.createElementNS(namespaces.cac, "cbc:Percent")
						cbcPercent.appendChild( document.createTextNode( items[item].getIgvPercentage() ) )
						cacTaxCategory.appendChild(cbcPercent)

						const cbcTaxExemptionReasonCode = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxExemptionReasonCode")
						cbcTaxExemptionReasonCode.appendChild( document.createTextNode( "10" ) )
						cacTaxCategory.appendChild(cbcTaxExemptionReasonCode)

						const cacTaxScheme = xmlDocument.createElementNS(namespaces.cac, "cac:TaxScheme")
						cacTaxCategory.appendChild(cacTaxScheme)
						{
							const cbcID = xmlDocument.createElementNS(namespaces.cac, "cbc:ID")
							cbcID.appendChild( document.createTextNode( "1000" ) )
							cacTaxScheme.appendChild(cbcID)

							const cbcName = xmlDocument.createElementNS(namespaces.cac, "cbc:Name")
							cbcName.appendChild( document.createTextNode( "IGV" ) )
							cacTaxScheme.appendChild(cbcName)

							const cbcTaxTypeCode = xmlDocument.createElementNS(namespaces.cac, "cbc:TaxTypeCode")
							cbcTaxTypeCode.appendChild( document.createTextNode( "VAT" ) )
							cacTaxScheme.appendChild(cbcTaxTypeCode)
						}
					}
				}
			}

			{ //Item
				const cacItem = xmlDocument.createElementNS(namespaces.cac, "cac:Item")
				cacInvoiceLine.appendChild(cacItem)

				const cbcDescription = xmlDocument.createElementNS(namespaces.cbc, "cbc:Description")
				cbcDescription.appendChild( document.createTextNode(items[item].getDescription()) )
				cacItem.appendChild(cbcDescription)

				const cacSellersItemIdentification = xmlDocument.createElementNS(namespaces.cac, "cac:SellersItemIdentification")
				cacItem.appendChild(cacSellersItemIdentification)

				const cbcID = xmlDocument.createElementNS(namespaces.cbc, "cbc:ID")
				cbcID.appendChild( document.createTextNode(items[item].getCode()) )
				cacSellersItemIdentification.appendChild(cbcID)

				const cacCommodityClassification = xmlDocument.createElementNS(namespaces.cac, "cac:CommodityClassification")
				cacItem.appendChild(cacCommodityClassification)

				const cbcItemClassificationCode = xmlDocument.createElementNS(namespaces.cbc, "cbc:ItemClassificationCode")
				cbcItemClassificationCode.setAttribute("listID", "UNSPSC")
				cbcItemClassificationCode.setAttribute("listAgencyName", "GS1 US")
				cbcItemClassificationCode.setAttribute("listName", "Item Classification")
				cbcItemClassificationCode.appendChild( document.createTextNode(items[item].getUnitCode()) )
				cacCommodityClassification.appendChild(cbcItemClassificationCode)
			}

			{ //Price
				const cacPrice = xmlDocument.createElementNS(namespaces.cac, "cac:Price")
				cacInvoiceLine.appendChild(cacPrice)

				const cbcPriceAmount = xmlDocument.createElementNS(namespaces.cbc, "cbc:PriceAmount")
				cbcPriceAmount.setAttribute("currencyID", "PEN")
				cbcPriceAmount.appendChild( document.createTextNode(items[item].getUnitValue(true)) )
			}
		}
	}

	this.sign = async function(algorithmName, isEnveloped = false, hashAlgorithm = "SHA-256", canonMethod = "c14n") {
		if(xmlDocument == undefined) {
			throw new Error("Documento XML no existe.")
		}
		const alg = getAlgorithm(algorithmName)

		// Read cert
		const certDer = pem2der(taxpayer.getCert())

		// Read key
		const keyDer = pem2der(taxpayer.getKey())
		const key = await window.crypto.subtle.importKey("pkcs8", keyDer, alg, true, ["sign"])

		const x509 = preparePem(taxpayer.getCert())

		const transforms = []
		if(isEnveloped) {
			transforms.push("enveloped")
		}
		transforms.push(canonMethod)

		return Promise.resolve()
			.then(function () {
				const signature = new XAdES.SignedXml()

				return signature.Sign(
					alg,        // algorithm
					key,        // key
					xmlDocument,// document
					{           // options
						references: [
							{ uri: "", hash: hashAlgorithm, transforms: transforms }
						],
						x509: [x509],
						//~ signerRole: { claimed: ["BOSS"] },
						signingCertificate: x509
					}
				)
			})
			.then(function(signature) {
				// Add signature to document
				const xmlEl = xmlDocument.getElementsByTagNameNS("urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2", "ExtensionContent")[0]
				xmlEl.appendChild(signature.GetXml())
				return true
			})
			.catch(function (e) {
				console.error(e)
				return false
			})
	}
}

var PaymentTerm = function() {
}

var Item = function(_description) {
	var description
	var code
	var quantity
	var unitValue
	var unitCode
	var currencyId
	var igvPercentage, iscPercentage
	var taxableIgvAmount
	var igvAmount, iscAmount
	var taxTotalAmount
	var lineExtensionAmount, pricingReferenceAmount
	var typePriceCode

	this.getPriceTypeCode = function() {
		return typePriceCode
	}

	this.getDescription = function() {
		return `<![CDATA[${description}]]>`
	}

	this.setDescription = function(d) {
		if( ( typeof d === "string" || d instanceof String ) && d.length > 0 ) {
			description = d
			return
		}
		throw new Error("No hay descripción válida.")
	}

	this.setCode = function(c) {
		code = c
	}

	this.getCode = function() {
		return code
	}

	this.getQuantity = function(withFormat = false) {
		return withFormat ? quantity.toFixed(2) : quantity
	}

	this.setQuantity = function(q) {
		quantity = parseFloat(q)
		if(isNaN(quantity)) {
			throw new Error("Cantidad no es un número.")
		}
	}

	this.getLineExtensionAmount = function(withFormat = false) {
		return withFormat ? lineExtensionAmount.toFixed(2) : lineExtensionAmount
	}

	/**
	 * According roll 03.
	 */
	this.setUnitCode = function(uc) {
	}

	this.getUnitCode = function() {
		return unitCode
	}

	this.getCurrencyId = function() {
		return currencyId
	}

	this.getIscPercentage = function() {
		return iscPercentage
	}

	this.setIscPercentage = function(ip) {
		if(ip >= 0 || ip <= 100) {
			iscPercentage = ip
			return
		}
		throw new Error("Porcentaje ISC inconsistente.")
	}

	this.getIscAmount = function(withFormat = false) {
		return withFormat ? iscAmount.toFixed(2) : iscAmount
	}

	this.setIgvPercentage = function(ip) {
		if(ip >= 0 || ip <= 100) {
			igvPercentage = ip
			return
		}
		throw new Error("Porcentaje IGV inconsistente.")
	}

	this.getIgvPercentage = function() {
		return igvPercentage
	}

	this.getIgvAmount = function(withFormat = false) {
		return withFormat ? igvAmount.toFixed(2) : igvAmount
	}

	this.getUnitValue = function(withFormat = false) {
		return withFormat ? unitValue.toFixed(2) : unitValue
	}

	this.setUnitValue = function(uv, withoutIgv) {
		uv = parseFloat(uv)
		if(!withoutIgv) {
			unitValue = uv
		}
		else {
			if(isNaN(igvPercentage)) {
				throw new Error("Se requiere previamnte pPorcentaje del IGV.")
			}
			unitValue = uv / ( 1 + igvPercentage / 100 )
		}
	}

	this.calcMounts = function() {
		//Todo esto asumiendo que el valorUnitario no tiene incluido el IGV.
		//~ (auxiliar) valorVenta = cantidad * valorUnitario
		lineExtensionAmount = quantity * unitValue

		let decimalIscPercentage = iscPercentage / 100 // eg: 0.17
		let decimalIgvPercentage = igvPercentage / 100 // eg: 0.18

		pricingReferenceAmount = unitValue * (1 + decimalIscPercentage) * (1 + decimalIgvPercentage)

		//~ valorISC = (porcentajeISC/100)×valorVenta
		iscAmount = decimalIscPercentage * lineExtensionAmount

		//~ valorIGV=(porcentajeIGV/100)×(valorVenta + valorISC)
		taxableIgvAmount = iscAmount + lineExtensionAmount
		igvAmount = taxableIgvAmount * decimalIgvPercentage

		taxTotalAmount = iscAmount + igvAmount
	}

	this.getTaxableIgvAmount = function(withFormat) {
		return withFormat ? taxableIgvAmount.toFixed(2) : taxableIgvAmount
	}

	this.getPricingReferenceAmount = function(withFormat = false) {
		return withFormat ? pricingReferenceAmount.toFixed(2) : pricingReferenceAmount
	}

	this.getTaxTotalAmount = function(withFormat = false) {
		return withFormat ? taxTotalAmount.toFixed(2) : taxTotalAmount
	}

	this.getTotalAmountPlusTaxTotal = function(withFormat = false) {
		return withFormat ? (igvAmount + iscAmount).toFixed(2) : igvAmount + iscAmount
	}

	this.setDescription(_description)
}
