/*----------------------------------------------------------- Table of Contents -----------------------------------------------------------
 cell-title
 header-packPacks
 cell-subgroup-ss_group
 header
 upload-file
 cell-label
 cell-cardMastery-pack
 heading-actions
 cell-id-ss_group
 cell-expandMembers-ss_group
 cell-id
 header-groupGroups
 cell-name-ss_user
 heading-actions-ss_user
 groups
 cell-id-ss_user
 create-entity
 header-basic
 heading-properties-pack
 cells
 heading-groups
 row
 cell-parent-ss_group
 cell-actions-ss_group
 cell-actions-pack
 heading-name
 cell-name-ss_group
 header-newPack
 heading-users
 cell-title-ss_group
 cell-actions
 cell-collection
 footer-packPacks
 cell-invite-ss_group
 cell-preview-card
 tab
 cell-roles
 footer-packCards
 footer-groupGroups
 footer-newPack
 cell-status-pack
 cell-id-pack
 row-card
 cell-correct-card
 header-createSubGroups
 cell-idEdit-pack
 cell-idEdit-ss_group
 cell-name-card
 cell-title-pack
 cell-titleNew-pack
 footer-subGroups
 cell-generic
 cell-packMastery-pack
 heading-packs
 header-subGroups
 results
 header-newGroup
 pack-publish
 footer-groupPacks
 heading-status
 cell-packList-pack
 cell-actions-card
 footer
 cell-expandMembers-pack
 heading
 heading-id
 cell-idTiles-ss_group
 dialog
 cell-parentOptions-ss_group
 cell-name-pack
 cell-idTiles-pack
 cell-retention-pack
 cell-actions-ss_user
 cell-properties-pack
 cell-collectionRow
 packs
 header-groupPacks
 cell-actionsGroup-pack
 cell-id-card
 cell-groups
 heading-roles
 header-search
 header-packCards
 cell-packList-ss_group
 add-entity
 footer-newGroup
 footer-groupCount */
(function (jQuery) {
// ^ scope for functions below, so we don't override anything

// TODO port entities here
    window.AdminController = {};
    window.AdminController.toFirewalledEntityArray = function (e) { return e; };
    window.AdminController.__vars = {};
    window.AdminController.__vars.radioCounter = 100000000;
    window.AdminController.__vars.defaultMiniTables = JSON.parse('{\"pack\":[\"title\",\"userCountStr\",\"cardCountStr\",\"id\",\"status\"],\"ss_user\":[\"first\",\"last\",\"email\",\"id\",\"deleted\"],\"ss_group\":[\"name\",\"userCountStr\",\"descriptionStr\",\"id\",\"deleted\"]}');
    window.AdminController.__vars.allTables = JSON.parse('{\"ss_user\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":{\"sessionCount\":{\"name\":\"sessionCount\",\"query\":\"SELECT COUNT(*) AS sessions FROM ss_user INNER JOIN visit ON id = user_id GROUP BY session_id\",\"resultClass\":null,\"resultSetMapping\":\"mappingSessionCount\",\"isSelfClass\":false}},\"sqlResultSetMappings\":{\"mappingSessionCount\":{\"name\":\"mappingSessionCount\",\"entities\":[],\"columns\":[{\"name\":\"sessions\"}]}},\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"username\":{\"fieldName\":\"username\",\"type\":\"string\",\"columnName\":\"username\",\"length\":255,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"usernameCanonical\":{\"fieldName\":\"usernameCanonical\",\"type\":\"string\",\"columnName\":\"username_canonical\",\"length\":255,\"unique\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"email\":{\"fieldName\":\"email\",\"type\":\"string\",\"columnName\":\"email\",\"length\":255,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"emailCanonical\":{\"fieldName\":\"emailCanonical\",\"type\":\"string\",\"columnName\":\"email_canonical\",\"length\":255,\"unique\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"enabled\":{\"fieldName\":\"enabled\",\"type\":\"boolean\",\"columnName\":\"enabled\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"salt\":{\"fieldName\":\"salt\",\"type\":\"string\",\"columnName\":\"salt\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"password\":{\"fieldName\":\"password\",\"type\":\"string\",\"columnName\":\"password\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"lastLogin\":{\"fieldName\":\"lastLogin\",\"type\":\"datetime\",\"columnName\":\"last_login\",\"nullable\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"locked\":{\"fieldName\":\"locked\",\"type\":\"boolean\",\"columnName\":\"locked\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expired\":{\"fieldName\":\"expired\",\"type\":\"boolean\",\"columnName\":\"expired\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expiresAt\":{\"fieldName\":\"expiresAt\",\"type\":\"datetime\",\"columnName\":\"expires_at\",\"nullable\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"confirmationToken\":{\"fieldName\":\"confirmationToken\",\"type\":\"string\",\"columnName\":\"confirmation_token\",\"nullable\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"passwordRequestedAt\":{\"fieldName\":\"passwordRequestedAt\",\"type\":\"datetime\",\"columnName\":\"password_requested_at\",\"nullable\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"roles\":{\"fieldName\":\"roles\",\"type\":\"array\",\"columnName\":\"roles\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpired\":{\"fieldName\":\"credentialsExpired\",\"type\":\"boolean\",\"columnName\":\"credentials_expired\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpireAt\":{\"fieldName\":\"credentialsExpireAt\",\"type\":\"datetime\",\"columnName\":\"credentials_expire_at\",\"nullable\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"id\":true,\"columnName\":\"id\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"lastVisit\":{\"fieldName\":\"lastVisit\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"last_visit\"},\"first\":{\"fieldName\":\"first\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"first\"},\"last\":{\"fieldName\":\"last\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"last\"},\"facebook_id\":{\"fieldName\":\"facebook_id\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"facebook_id\"},\"facebook_access_token\":{\"fieldName\":\"facebook_access_token\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"facebook_access_token\"},\"google_id\":{\"fieldName\":\"google_id\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"google_id\"},\"google_access_token\":{\"fieldName\":\"google_access_token\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"google_access_token\"},\"evernote_id\":{\"fieldName\":\"evernote_id\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"evernote_id\"},\"evernote_access_token\":{\"fieldName\":\"evernote_access_token\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"evernote_access_token\"},\"gcal_id\":{\"fieldName\":\"gcal_id\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"gcal_id\"},\"gcal_access_token\":{\"fieldName\":\"gcal_access_token\",\"type\":\"string\",\"scale\":0,\"length\":255,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"gcal_access_token\"},\"devices\":{\"fieldName\":\"devices\",\"type\":\"simple_array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"devices\"},\"properties\":{\"fieldName\":\"properties\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"properties\"}},\"fieldNames\":{\"username\":\"username\",\"username_canonical\":\"usernameCanonical\",\"email\":\"email\",\"email_canonical\":\"emailCanonical\",\"enabled\":\"enabled\",\"salt\":\"salt\",\"password\":\"password\",\"last_login\":\"lastLogin\",\"locked\":\"locked\",\"expired\":\"expired\",\"expires_at\":\"expiresAt\",\"confirmation_token\":\"confirmationToken\",\"password_requested_at\":\"passwordRequestedAt\",\"roles\":\"roles\",\"credentials_expired\":\"credentialsExpired\",\"credentials_expire_at\":\"credentialsExpireAt\",\"id\":\"id\",\"created\":\"created\",\"last_visit\":\"lastVisit\",\"first\":\"first\",\"last\":\"last\",\"facebook_id\":\"facebook_id\",\"facebook_access_token\":\"facebook_access_token\",\"google_id\":\"google_id\",\"google_access_token\":\"google_access_token\",\"evernote_id\":\"evernote_id\",\"evernote_access_token\":\"evernote_access_token\",\"gcal_id\":\"gcal_id\",\"gcal_access_token\":\"gcal_access_token\",\"devices\":\"devices\",\"properties\":\"properties\"},\"columnNames\":{\"username\":\"username\",\"usernameCanonical\":\"username_canonical\",\"email\":\"email\",\"emailCanonical\":\"email_canonical\",\"enabled\":\"enabled\",\"salt\":\"salt\",\"password\":\"password\",\"lastLogin\":\"last_login\",\"locked\":\"locked\",\"expired\":\"expired\",\"expiresAt\":\"expires_at\",\"confirmationToken\":\"confirmation_token\",\"passwordRequestedAt\":\"password_requested_at\",\"roles\":\"roles\",\"credentialsExpired\":\"credentials_expired\",\"credentialsExpireAt\":\"credentials_expire_at\",\"id\":\"id\",\"created\":\"created\",\"lastVisit\":\"last_visit\",\"first\":\"first\",\"last\":\"last\",\"facebook_id\":\"facebook_id\",\"facebook_access_token\":\"facebook_access_token\",\"google_id\":\"google_id\",\"google_access_token\":\"google_access_token\",\"evernote_id\":\"evernote_id\",\"evernote_access_token\":\"evernote_access_token\",\"gcal_id\":\"gcal_id\",\"gcal_access_token\":\"gcal_access_token\",\"devices\":\"devices\",\"properties\":\"properties\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"ss_user\",\"uniqueConstraints\":{\"email_idx\":{\"columns\":[\"email\"]},\"username_idx\":{\"columns\":[\"username\"]}}},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"payments\":{\"fieldName\":\"payments\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"visits\":{\"fieldName\":\"visits\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"invites\":{\"fieldName\":\"invites\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"invitees\":{\"fieldName\":\"invitees\",\"mappedBy\":\"invitee\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"authored\":{\"fieldName\":\"authored\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"userPacks\":{\"fieldName\":\"userPacks\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"files\":{\"fieldName\":\"files\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"responses\":{\"fieldName\":\"responses\",\"mappedBy\":\"user\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"photo\":{\"fieldName\":\"photo\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"joinColumns\":[{\"name\":\"file_id\",\"unique\":true,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"mappedBy\":null,\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"type\":1,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"file_id\":\"id\"},\"joinColumnFieldNames\":{\"file_id\":\"file_id\"},\"targetToSourceKeyColumns\":{\"id\":\"file_id\"}},\"groups\":{\"fieldName\":\"groups\",\"joinTable\":{\"name\":\"ss_user_group\",\"schema\":null,\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"inverseJoinColumns\":[{\"name\":\"group_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}]},\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"mappedBy\":null,\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"type\":8,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"joinTableColumns\":[\"user_id\",\"group_id\"],\"relationToSourceKeyColumns\":{\"user_id\":\"id\"},\"relationToTargetKeyColumns\":{\"group_id\":\"id\"}}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"isReadOnly\":false,\"reflFields\":{\"username\":{\"name\":\"username\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"usernameCanonical\":{\"name\":\"usernameCanonical\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"email\":{\"name\":\"email\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"emailCanonical\":{\"name\":\"emailCanonical\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"enabled\":{\"name\":\"enabled\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"salt\":{\"name\":\"salt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"password\":{\"name\":\"password\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"lastLogin\":{\"name\":\"lastLogin\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"locked\":{\"name\":\"locked\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expired\":{\"name\":\"expired\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expiresAt\":{\"name\":\"expiresAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"confirmationToken\":{\"name\":\"confirmationToken\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"passwordRequestedAt\":{\"name\":\"passwordRequestedAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"roles\":{\"name\":\"roles\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpired\":{\"name\":\"credentialsExpired\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpireAt\":{\"name\":\"credentialsExpireAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"lastVisit\":{\"name\":\"lastVisit\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"first\":{\"name\":\"first\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"last\":{\"name\":\"last\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"facebook_id\":{\"name\":\"facebook_id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"facebook_access_token\":{\"name\":\"facebook_access_token\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"google_id\":{\"name\":\"google_id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"google_access_token\":{\"name\":\"google_access_token\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"evernote_id\":{\"name\":\"evernote_id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"evernote_access_token\":{\"name\":\"evernote_access_token\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"gcal_id\":{\"name\":\"gcal_id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"gcal_access_token\":{\"name\":\"gcal_access_token\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"devices\":{\"name\":\"devices\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"properties\":{\"name\":\"properties\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"payments\":{\"name\":\"payments\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"visits\":{\"name\":\"visits\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"invites\":{\"name\":\"invites\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"invitees\":{\"name\":\"invitees\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"authored\":{\"name\":\"authored\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"userPacks\":{\"name\":\"userPacks\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"files\":{\"name\":\"files\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"responses\":{\"name\":\"responses\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"photo\":{\"name\":\"photo\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"},\"groups\":{\"name\":\"groups\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\"}}},\"payment\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"amount\":{\"fieldName\":\"amount\",\"type\":\"string\",\"scale\":0,\"length\":12,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"amount\"},\"first\":{\"fieldName\":\"first\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"first\"},\"last\":{\"fieldName\":\"last\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"last\"},\"email\":{\"fieldName\":\"email\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"email\"},\"payment\":{\"fieldName\":\"payment\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"payment\"},\"subscription\":{\"fieldName\":\"subscription\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"subscription\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"deleted\":{\"fieldName\":\"deleted\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"deleted\"}},\"fieldNames\":{\"id\":\"id\",\"amount\":\"amount\",\"first\":\"first\",\"last\":\"last\",\"email\":\"email\",\"payment\":\"payment\",\"subscription\":\"subscription\",\"created\":\"created\",\"deleted\":\"deleted\"},\"columnNames\":{\"id\":\"id\",\"amount\":\"amount\",\"first\":\"first\",\"last\":\"last\",\"email\":\"email\",\"payment\":\"payment\",\"subscription\":\"subscription\",\"created\":\"created\",\"deleted\":\"deleted\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"payment\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"payments\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"pack\":{\"fieldName\":\"pack\",\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"payments\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"pack_id\":\"id\"},\"joinColumnFieldNames\":{\"pack_id\":\"pack_id\"},\"targetToSourceKeyColumns\":{\"id\":\"pack_id\"},\"orphanRemoval\":false},\"coupon\":{\"fieldName\":\"coupon\",\"joinColumns\":[{\"name\":\"coupon_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"payments\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"coupon_id\":\"id\"},\"joinColumnFieldNames\":{\"coupon_id\":\"coupon_id\"},\"targetToSourceKeyColumns\":{\"id\":\"coupon_id\"},\"orphanRemoval\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"amount\":{\"name\":\"amount\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"first\":{\"name\":\"first\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"last\":{\"name\":\"last\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"email\":{\"name\":\"email\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"payment\":{\"name\":\"payment\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"subscription\":{\"name\":\"subscription\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"deleted\":{\"name\":\"deleted\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"pack\":{\"name\":\"pack\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"},\"coupon\":{\"name\":\"coupon\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\"}}},\"visit\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"id\":true,\"columnName\":\"id\"},\"session\":{\"fieldName\":\"session\",\"type\":\"string\",\"scale\":0,\"length\":64,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"session_id\"},\"path\":{\"fieldName\":\"path\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"path\"},\"query\":{\"fieldName\":\"query\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"query\"},\"hash\":{\"fieldName\":\"hash\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"hash\"},\"method\":{\"fieldName\":\"method\",\"type\":\"string\",\"scale\":0,\"length\":8,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"method\"},\"ip\":{\"fieldName\":\"ip\",\"type\":\"integer\",\"scale\":0,\"length\":12,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"ip\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"}},\"fieldNames\":{\"id\":\"id\",\"session_id\":\"session\",\"path\":\"path\",\"query\":\"query\",\"hash\":\"hash\",\"method\":\"method\",\"ip\":\"ip\",\"created\":\"created\"},\"columnNames\":{\"id\":\"id\",\"session\":\"session_id\",\"path\":\"path\",\"query\":\"query\",\"hash\":\"hash\",\"method\":\"method\",\"ip\":\"ip\",\"created\":\"created\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"visit\",\"indexes\":{\"session_idx\":{\"columns\":[\"session_id\",\"user_id\"]},\"path_idx\":{\"columns\":[\"path\",\"session_id\",\"user_id\"]}}},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"visits\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"session\":{\"name\":\"session\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"path\":{\"name\":\"path\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"query\":{\"name\":\"query\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"hash\":{\"name\":\"hash\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"method\":{\"name\":\"method\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"ip\":{\"name\":\"ip\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\"}}},\"invite\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"first\":{\"fieldName\":\"first\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"first\"},\"last\":{\"fieldName\":\"last\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"last\"},\"email\":{\"fieldName\":\"email\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"email\"},\"activated\":{\"fieldName\":\"activated\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"activated\"},\"code\":{\"fieldName\":\"code\",\"type\":\"string\",\"scale\":0,\"length\":64,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"code\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"reminder\":{\"fieldName\":\"reminder\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"reminder\"},\"properties\":{\"fieldName\":\"properties\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"properties\"}},\"fieldNames\":{\"id\":\"id\",\"first\":\"first\",\"last\":\"last\",\"email\":\"email\",\"activated\":\"activated\",\"code\":\"code\",\"created\":\"created\",\"reminder\":\"reminder\",\"properties\":\"properties\"},\"columnNames\":{\"id\":\"id\",\"first\":\"first\",\"last\":\"last\",\"email\":\"email\",\"activated\":\"activated\",\"code\":\"code\",\"created\":\"created\",\"reminder\":\"reminder\",\"properties\":\"properties\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"invite\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"group\":{\"fieldName\":\"group\",\"joinColumns\":[{\"name\":\"group_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"invites\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"group_id\":\"id\"},\"joinColumnFieldNames\":{\"group_id\":\"group_id\"},\"targetToSourceKeyColumns\":{\"id\":\"group_id\"},\"orphanRemoval\":false},\"pack\":{\"fieldName\":\"pack\",\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"invites\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"pack_id\":\"id\"},\"joinColumnFieldNames\":{\"pack_id\":\"pack_id\"},\"targetToSourceKeyColumns\":{\"id\":\"pack_id\"},\"orphanRemoval\":false},\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"invites\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"invitee\":{\"fieldName\":\"invitee\",\"joinColumns\":[{\"name\":\"invitee_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"invitees\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"invitee_id\":\"id\"},\"joinColumnFieldNames\":{\"invitee_id\":\"invitee_id\"},\"targetToSourceKeyColumns\":{\"id\":\"invitee_id\"},\"orphanRemoval\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"first\":{\"name\":\"first\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"last\":{\"name\":\"last\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"email\":{\"name\":\"email\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"activated\":{\"name\":\"activated\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"code\":{\"name\":\"code\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"reminder\":{\"name\":\"reminder\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"properties\":{\"name\":\"properties\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"group\":{\"name\":\"group\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"pack\":{\"name\":\"pack\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"},\"invitee\":{\"name\":\"invitee\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\"}}},\"pack\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"properties\":{\"fieldName\":\"properties\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"properties\"},\"downloads\":{\"fieldName\":\"downloads\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"downloads\"},\"rating\":{\"fieldName\":\"rating\",\"type\":\"decimal\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"rating\"},\"priority\":{\"fieldName\":\"priority\",\"type\":\"decimal\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"priority\"},\"activeFrom\":{\"fieldName\":\"activeFrom\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"active_from\"},\"activeTo\":{\"fieldName\":\"activeTo\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"active_to\"},\"status\":{\"fieldName\":\"status\",\"type\":\"string\",\"scale\":0,\"length\":16,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"status\"},\"price\":{\"fieldName\":\"price\",\"type\":\"decimal\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"price\"},\"title\":{\"fieldName\":\"title\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"title\"},\"description\":{\"fieldName\":\"description\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"description\"},\"tags\":{\"fieldName\":\"tags\",\"type\":\"simple_array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"tags\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"modified\":{\"fieldName\":\"modified\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"modified\"}},\"fieldNames\":{\"id\":\"id\",\"properties\":\"properties\",\"downloads\":\"downloads\",\"rating\":\"rating\",\"priority\":\"priority\",\"active_from\":\"activeFrom\",\"active_to\":\"activeTo\",\"status\":\"status\",\"price\":\"price\",\"title\":\"title\",\"description\":\"description\",\"tags\":\"tags\",\"created\":\"created\",\"modified\":\"modified\"},\"columnNames\":{\"id\":\"id\",\"properties\":\"properties\",\"downloads\":\"downloads\",\"rating\":\"rating\",\"priority\":\"priority\",\"activeFrom\":\"active_from\",\"activeTo\":\"active_to\",\"status\":\"status\",\"price\":\"price\",\"title\":\"title\",\"description\":\"description\",\"tags\":\"tags\",\"created\":\"created\",\"modified\":\"modified\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"pack\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"group\":{\"fieldName\":\"group\",\"joinColumns\":[{\"name\":\"group_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"packs\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"group_id\":\"id\"},\"joinColumnFieldNames\":{\"group_id\":\"group_id\"},\"targetToSourceKeyColumns\":{\"id\":\"group_id\"},\"orphanRemoval\":false},\"groups\":{\"fieldName\":\"groups\",\"joinTable\":{\"name\":\"group_pack\",\"schema\":null,\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"inverseJoinColumns\":[{\"name\":\"group_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}]},\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"mappedBy\":null,\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"type\":8,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"joinTableColumns\":[\"pack_id\",\"group_id\"],\"relationToSourceKeyColumns\":{\"pack_id\":\"id\"},\"relationToTargetKeyColumns\":{\"group_id\":\"id\"}},\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"packs\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"userPacks\":{\"fieldName\":\"userPacks\",\"mappedBy\":\"pack\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"cards\":{\"fieldName\":\"cards\",\"mappedBy\":\"pack\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"properties\":{\"name\":\"properties\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"downloads\":{\"name\":\"downloads\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"rating\":{\"name\":\"rating\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"priority\":{\"name\":\"priority\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"activeFrom\":{\"name\":\"activeFrom\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"activeTo\":{\"name\":\"activeTo\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"status\":{\"name\":\"status\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"price\":{\"name\":\"price\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"title\":{\"name\":\"title\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"description\":{\"name\":\"description\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"tags\":{\"name\":\"tags\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"modified\":{\"name\":\"modified\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"group\":{\"name\":\"group\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"groups\":{\"name\":\"groups\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"userPacks\":{\"name\":\"userPacks\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"},\"cards\":{\"name\":\"cards\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\"}}},\"user_pack\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"user\",\"pack\"],\"inheritanceType\":1,\"generatorType\":5,\"fieldMappings\":{\"priority\":{\"fieldName\":\"priority\",\"type\":\"decimal\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"priority\"},\"retryFrom\":{\"fieldName\":\"retryFrom\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"retry_from\"},\"retryTo\":{\"fieldName\":\"retryTo\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"retry_to\"},\"downloaded\":{\"fieldName\":\"downloaded\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"downloaded\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"removed\":{\"fieldName\":\"removed\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"removed\"}},\"fieldNames\":{\"priority\":\"priority\",\"retry_from\":\"retryFrom\",\"retry_to\":\"retryTo\",\"downloaded\":\"downloaded\",\"created\":\"created\",\"removed\":\"removed\"},\"columnNames\":{\"priority\":\"priority\",\"retryFrom\":\"retry_from\",\"retryTo\":\"retry_to\",\"downloaded\":\"downloaded\",\"created\":\"created\",\"removed\":\"removed\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"user_pack\",\"uniqueConstraints\":{\"username_idx\":{\"columns\":[\"user_id\",\"pack_id\"]}}},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"user\":{\"fieldName\":\"user\",\"id\":true,\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"userPacks\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":4,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"pack\":{\"fieldName\":\"pack\",\"id\":true,\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"userPacks\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"fetch\":4,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"pack_id\":\"id\"},\"joinColumnFieldNames\":{\"pack_id\":\"pack_id\"},\"targetToSourceKeyColumns\":{\"id\":\"pack_id\"},\"orphanRemoval\":false}},\"isIdentifierComposite\":true,\"containsForeignIdentifier\":true,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"isReadOnly\":false,\"reflFields\":{\"priority\":{\"name\":\"priority\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"retryFrom\":{\"name\":\"retryFrom\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"retryTo\":{\"name\":\"retryTo\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"downloaded\":{\"name\":\"downloaded\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"removed\":{\"name\":\"removed\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"},\"pack\":{\"name\":\"pack\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\UserPack\"}}},\"file\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"filename\":{\"fieldName\":\"filename\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"filename\"},\"uploadId\":{\"fieldName\":\"uploadId\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"upload_id\"},\"url\":{\"fieldName\":\"url\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"url\"},\"parts\":{\"fieldName\":\"parts\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"parts\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"}},\"fieldNames\":{\"id\":\"id\",\"filename\":\"filename\",\"upload_id\":\"uploadId\",\"url\":\"url\",\"parts\":\"parts\",\"created\":\"created\"},\"columnNames\":{\"id\":\"id\",\"filename\":\"filename\",\"uploadId\":\"upload_id\",\"url\":\"url\",\"parts\":\"parts\",\"created\":\"created\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"file\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"files\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"response\":{\"fieldName\":\"response\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"joinColumns\":[],\"mappedBy\":\"file\",\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"type\":1,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"filename\":{\"name\":\"filename\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"uploadId\":{\"name\":\"uploadId\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"url\":{\"name\":\"url\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"parts\":{\"name\":\"parts\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"},\"response\":{\"name\":\"response\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\"}}},\"response\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"value\":{\"fieldName\":\"value\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"value\"},\"correct\":{\"fieldName\":\"correct\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"correct\"}},\"fieldNames\":{\"id\":\"id\",\"created\":\"created\",\"value\":\"value\",\"correct\":\"correct\"},\"columnNames\":{\"id\":\"id\",\"created\":\"created\",\"value\":\"value\",\"correct\":\"correct\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"response\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"card\":{\"fieldName\":\"card\",\"joinColumns\":[{\"name\":\"card_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"responses\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"card_id\":\"id\"},\"joinColumnFieldNames\":{\"card_id\":\"card_id\"},\"targetToSourceKeyColumns\":{\"id\":\"card_id\"},\"orphanRemoval\":false},\"answer\":{\"fieldName\":\"answer\",\"joinColumns\":[{\"name\":\"answer_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"responses\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"answer_id\":\"id\"},\"joinColumnFieldNames\":{\"answer_id\":\"answer_id\"},\"targetToSourceKeyColumns\":{\"id\":\"answer_id\"},\"orphanRemoval\":false},\"user\":{\"fieldName\":\"user\",\"joinColumns\":[{\"name\":\"user_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"responses\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"user_id\":\"id\"},\"joinColumnFieldNames\":{\"user_id\":\"user_id\"},\"targetToSourceKeyColumns\":{\"id\":\"user_id\"},\"orphanRemoval\":false},\"file\":{\"fieldName\":\"file\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"joinColumns\":[{\"name\":\"file_id\",\"unique\":true,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"mappedBy\":null,\"inversedBy\":\"response\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"type\":1,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"file_id\":\"id\"},\"joinColumnFieldNames\":{\"file_id\":\"file_id\"},\"targetToSourceKeyColumns\":{\"id\":\"file_id\"}}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"value\":{\"name\":\"value\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"correct\":{\"name\":\"correct\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"card\":{\"name\":\"card\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"answer\":{\"name\":\"answer\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"user\":{\"name\":\"user\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"},\"file\":{\"name\":\"file\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\"}}},\"ss_group\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"name\":{\"fieldName\":\"name\",\"type\":\"string\",\"columnName\":\"name\",\"length\":255,\"unique\":true,\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"roles\":{\"fieldName\":\"roles\",\"type\":\"array\",\"columnName\":\"roles\",\"declared\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"description\":{\"fieldName\":\"description\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"description\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"deleted\":{\"fieldName\":\"deleted\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"deleted\"}},\"fieldNames\":{\"name\":\"name\",\"roles\":\"roles\",\"id\":\"id\",\"description\":\"description\",\"created\":\"created\",\"deleted\":\"deleted\"},\"columnNames\":{\"name\":\"name\",\"roles\":\"roles\",\"id\":\"id\",\"description\":\"description\",\"created\":\"created\",\"deleted\":\"deleted\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"ss_group\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"coupons\":{\"fieldName\":\"coupons\",\"mappedBy\":\"group\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"invites\":{\"fieldName\":\"invites\",\"mappedBy\":\"group\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Invite\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"packs\":{\"fieldName\":\"packs\",\"mappedBy\":\"group\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"groupPacks\":{\"fieldName\":\"groupPacks\",\"joinTable\":[],\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"mappedBy\":\"groups\",\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"type\":8,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"users\":{\"fieldName\":\"users\",\"joinTable\":[],\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\User\",\"mappedBy\":\"groups\",\"inversedBy\":null,\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"type\":8,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"logo\":{\"fieldName\":\"logo\",\"joinColumns\":[{\"name\":\"file_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":null,\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\File\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"file_id\":\"id\"},\"joinColumnFieldNames\":{\"file_id\":\"file_id\"},\"targetToSourceKeyColumns\":{\"id\":\"file_id\"},\"orphanRemoval\":false},\"parent\":{\"fieldName\":\"parent\",\"joinColumns\":[{\"name\":\"parent\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":null,\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"parent\":\"id\"},\"joinColumnFieldNames\":{\"parent\":\"parent\"},\"targetToSourceKeyColumns\":{\"id\":\"parent\"},\"orphanRemoval\":false},\"subgroups\":{\"fieldName\":\"subgroups\",\"mappedBy\":\"parent\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"isReadOnly\":false,\"reflFields\":{\"name\":{\"name\":\"name\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"roles\":{\"name\":\"roles\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"description\":{\"name\":\"description\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"deleted\":{\"name\":\"deleted\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"coupons\":{\"name\":\"coupons\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"invites\":{\"name\":\"invites\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"packs\":{\"name\":\"packs\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"groupPacks\":{\"name\":\"groupPacks\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"users\":{\"name\":\"users\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"logo\":{\"name\":\"logo\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"parent\":{\"name\":\"parent\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"},\"subgroups\":{\"name\":\"subgroups\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\"}}},\"coupon\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"name\":{\"fieldName\":\"name\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"name\"},\"description\":{\"fieldName\":\"description\",\"type\":\"string\",\"scale\":0,\"length\":4096,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"description\"},\"options\":{\"fieldName\":\"options\",\"type\":\"array\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"options\"},\"validFrom\":{\"fieldName\":\"validFrom\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"valid_from\"},\"validTo\":{\"fieldName\":\"validTo\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"valid_to\"},\"maxUses\":{\"fieldName\":\"maxUses\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"max_uses\"},\"seed\":{\"fieldName\":\"seed\",\"type\":\"string\",\"scale\":0,\"length\":32,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"seed\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"deleted\":{\"fieldName\":\"deleted\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"deleted\"}},\"fieldNames\":{\"id\":\"id\",\"name\":\"name\",\"description\":\"description\",\"options\":\"options\",\"valid_from\":\"validFrom\",\"valid_to\":\"validTo\",\"max_uses\":\"maxUses\",\"seed\":\"seed\",\"created\":\"created\",\"deleted\":\"deleted\"},\"columnNames\":{\"id\":\"id\",\"name\":\"name\",\"description\":\"description\",\"options\":\"options\",\"validFrom\":\"valid_from\",\"validTo\":\"valid_to\",\"maxUses\":\"max_uses\",\"seed\":\"seed\",\"created\":\"created\",\"deleted\":\"deleted\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"coupon\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"group\":{\"fieldName\":\"group\",\"joinColumns\":[{\"name\":\"group_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"coupons\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Group\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"group_id\":\"id\"},\"joinColumnFieldNames\":{\"group_id\":\"group_id\"},\"targetToSourceKeyColumns\":{\"id\":\"group_id\"},\"orphanRemoval\":false},\"pack\":{\"fieldName\":\"pack\",\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"coupons\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"pack_id\":\"id\"},\"joinColumnFieldNames\":{\"pack_id\":\"pack_id\"},\"targetToSourceKeyColumns\":{\"id\":\"pack_id\"},\"orphanRemoval\":false},\"payments\":{\"fieldName\":\"payments\",\"mappedBy\":\"coupon\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Payment\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"name\":{\"name\":\"name\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"description\":{\"name\":\"description\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"options\":{\"name\":\"options\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"validFrom\":{\"name\":\"validFrom\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"validTo\":{\"name\":\"validTo\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"maxUses\":{\"name\":\"maxUses\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"seed\":{\"name\":\"seed\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"deleted\":{\"name\":\"deleted\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"group\":{\"name\":\"group\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"pack\":{\"name\":\"pack\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"},\"payments\":{\"name\":\"payments\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Coupon\"}}},\"card\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"modified\":{\"fieldName\":\"modified\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"modified\"},\"content\":{\"fieldName\":\"content\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"content\"},\"responseContent\":{\"fieldName\":\"responseContent\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"response_content\"},\"contentType\":{\"fieldName\":\"contentType\",\"type\":\"string\",\"scale\":0,\"length\":16,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"content_type\"},\"responseType\":{\"fieldName\":\"responseType\",\"type\":\"string\",\"scale\":0,\"length\":16,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"response_type\"},\"recurrence\":{\"fieldName\":\"recurrence\",\"type\":\"string\",\"scale\":0,\"length\":16,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"recurrence\"},\"deleted\":{\"fieldName\":\"deleted\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"deleted\"}},\"fieldNames\":{\"id\":\"id\",\"created\":\"created\",\"modified\":\"modified\",\"content\":\"content\",\"response_content\":\"responseContent\",\"content_type\":\"contentType\",\"response_type\":\"responseType\",\"recurrence\":\"recurrence\",\"deleted\":\"deleted\"},\"columnNames\":{\"id\":\"id\",\"created\":\"created\",\"modified\":\"modified\",\"content\":\"content\",\"responseContent\":\"response_content\",\"contentType\":\"content_type\",\"responseType\":\"response_type\",\"recurrence\":\"recurrence\",\"deleted\":\"deleted\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"card\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"pack\":{\"fieldName\":\"pack\",\"joinColumns\":[{\"name\":\"pack_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"cards\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Pack\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"pack_id\":\"id\"},\"joinColumnFieldNames\":{\"pack_id\":\"pack_id\"},\"targetToSourceKeyColumns\":{\"id\":\"pack_id\"},\"orphanRemoval\":false},\"responses\":{\"fieldName\":\"responses\",\"mappedBy\":\"card\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"cascade\":[],\"indexBy\":\"user\",\"orphanRemoval\":false,\"fetch\":4,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false},\"answers\":{\"fieldName\":\"answers\",\"mappedBy\":\"card\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"modified\":{\"name\":\"modified\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"content\":{\"name\":\"content\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"responseContent\":{\"name\":\"responseContent\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"contentType\":{\"name\":\"contentType\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"responseType\":{\"name\":\"responseType\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"recurrence\":{\"name\":\"recurrence\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"deleted\":{\"name\":\"deleted\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"pack\":{\"name\":\"pack\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"responses\":{\"name\":\"responses\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"},\"answers\":{\"name\":\"answers\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\"}}},\"answer\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"content\":{\"fieldName\":\"content\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"content\"},\"response\":{\"fieldName\":\"response\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"response\"},\"value\":{\"fieldName\":\"value\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"value\"},\"correct\":{\"fieldName\":\"correct\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"correct\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"},\"modified\":{\"fieldName\":\"modified\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":true,\"precision\":0,\"columnName\":\"modified\"},\"deleted\":{\"fieldName\":\"deleted\",\"type\":\"boolean\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"deleted\"}},\"fieldNames\":{\"id\":\"id\",\"content\":\"content\",\"response\":\"response\",\"value\":\"value\",\"correct\":\"correct\",\"created\":\"created\",\"modified\":\"modified\",\"deleted\":\"deleted\"},\"columnNames\":{\"id\":\"id\",\"content\":\"content\",\"response\":\"response\",\"value\":\"value\",\"correct\":\"correct\",\"created\":\"created\",\"modified\":\"modified\",\"deleted\":\"deleted\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"answer\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":{\"card\":{\"fieldName\":\"card\",\"joinColumns\":[{\"name\":\"card_id\",\"unique\":false,\"nullable\":true,\"onDelete\":null,\"columnDefinition\":null,\"referencedColumnName\":\"id\"}],\"cascade\":[],\"inversedBy\":\"answers\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Card\",\"fetch\":2,\"type\":2,\"mappedBy\":null,\"isOwningSide\":true,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false,\"sourceToTargetKeyColumns\":{\"card_id\":\"id\"},\"joinColumnFieldNames\":{\"card_id\":\"card_id\"},\"targetToSourceKeyColumns\":{\"id\":\"card_id\"},\"orphanRemoval\":false},\"responses\":{\"fieldName\":\"responses\",\"mappedBy\":\"answer\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Response\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"content\":{\"name\":\"content\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"response\":{\"name\":\"response\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"value\":{\"name\":\"value\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"correct\":{\"name\":\"correct\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"modified\":{\"name\":\"modified\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"deleted\":{\"name\":\"deleted\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"card\":{\"name\":\"card\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"},\"responses\":{\"name\":\"responses\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Answer\"}}},\"session\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":5,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"string\",\"scale\":0,\"length\":128,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"session_id\",\"id\":true},\"value\":{\"fieldName\":\"value\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"session_value\"},\"time\":{\"fieldName\":\"time\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"session_time\"},\"lifetime\":{\"fieldName\":\"lifetime\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"session_lifetime\"}},\"fieldNames\":{\"session_id\":\"id\",\"session_value\":\"value\",\"session_time\":\"time\",\"session_lifetime\":\"lifetime\"},\"columnNames\":{\"id\":\"session_id\",\"value\":\"session_value\",\"time\":\"session_time\",\"lifetime\":\"session_lifetime\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"session\"},\"lifecycleCallbacks\":[],\"entityListeners\":[],\"associationMappings\":{\"visits\":{\"fieldName\":\"visits\",\"mappedBy\":\"session\",\"targetEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Visit\",\"cascade\":[],\"orphanRemoval\":false,\"fetch\":2,\"orderBy\":{\"created\":\"DESC\"},\"type\":4,\"inversedBy\":null,\"isOwningSide\":false,\"sourceEntity\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\",\"isCascadeRemove\":false,\"isCascadePersist\":false,\"isCascadeRefresh\":false,\"isCascadeMerge\":false,\"isCascadeDetach\":false}},\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"},\"value\":{\"name\":\"value\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"},\"time\":{\"name\":\"time\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"},\"lifetime\":{\"name\":\"lifetime\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"},\"visits\":{\"name\":\"visits\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Session\"}}},\"mail\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\",\"namespace\":\"StudySauce\\\\Bundle\\\\Entity\",\"rootEntityName\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":false,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[\"id\"],\"inheritanceType\":1,\"generatorType\":4,\"fieldMappings\":{\"id\":{\"fieldName\":\"id\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"id\",\"id\":true},\"status\":{\"fieldName\":\"status\",\"type\":\"integer\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"status\"},\"message\":{\"fieldName\":\"message\",\"type\":\"text\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"message\"},\"environment\":{\"fieldName\":\"environment\",\"type\":\"string\",\"scale\":0,\"length\":256,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"environment\"},\"created\":{\"fieldName\":\"created\",\"type\":\"datetime\",\"scale\":0,\"length\":null,\"unique\":false,\"nullable\":false,\"precision\":0,\"columnName\":\"created\"}},\"fieldNames\":{\"id\":\"id\",\"status\":\"status\",\"message\":\"message\",\"environment\":\"environment\",\"created\":\"created\"},\"columnNames\":{\"id\":\"id\",\"status\":\"status\",\"message\":\"message\",\"environment\":\"environment\",\"created\":\"created\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"mail\"},\"lifecycleCallbacks\":{\"prePersist\":[\"setCreatedValue\"]},\"entityListeners\":[],\"associationMappings\":[],\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"},\"isReadOnly\":false,\"reflFields\":{\"id\":{\"name\":\"id\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"},\"status\":{\"name\":\"status\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"},\"message\":{\"name\":\"message\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"},\"environment\":{\"name\":\"environment\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"},\"created\":{\"name\":\"created\",\"class\":\"StudySauce\\\\Bundle\\\\Entity\\\\Mail\"}}},\"Group\":{\"name\":\"FOS\\\\UserBundle\\\\Model\\\\Group\",\"namespace\":\"FOS\\\\UserBundle\\\\Model\",\"rootEntityName\":\"FOS\\\\UserBundle\\\\Model\\\\Group\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":true,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[],\"inheritanceType\":1,\"generatorType\":5,\"fieldMappings\":{\"name\":{\"fieldName\":\"name\",\"type\":\"string\",\"columnName\":\"name\",\"length\":255,\"unique\":true},\"roles\":{\"fieldName\":\"roles\",\"type\":\"array\",\"columnName\":\"roles\"}},\"fieldNames\":{\"name\":\"name\",\"roles\":\"roles\"},\"columnNames\":{\"name\":\"name\",\"roles\":\"roles\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"Group\"},\"lifecycleCallbacks\":[],\"entityListeners\":[],\"associationMappings\":[],\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"isReadOnly\":false,\"reflFields\":{\"name\":{\"name\":\"name\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"},\"roles\":{\"name\":\"roles\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\Group\"}}},\"User\":{\"name\":\"FOS\\\\UserBundle\\\\Model\\\\User\",\"namespace\":\"FOS\\\\UserBundle\\\\Model\",\"rootEntityName\":\"FOS\\\\UserBundle\\\\Model\\\\User\",\"customGeneratorDefinition\":null,\"customRepositoryClassName\":null,\"isMappedSuperclass\":true,\"isEmbeddedClass\":false,\"parentClasses\":[],\"subClasses\":[],\"embeddedClasses\":[],\"namedQueries\":[],\"namedNativeQueries\":[],\"sqlResultSetMappings\":[],\"identifier\":[],\"inheritanceType\":1,\"generatorType\":5,\"fieldMappings\":{\"username\":{\"fieldName\":\"username\",\"type\":\"string\",\"columnName\":\"username\",\"length\":255},\"usernameCanonical\":{\"fieldName\":\"usernameCanonical\",\"type\":\"string\",\"columnName\":\"username_canonical\",\"length\":255,\"unique\":true},\"email\":{\"fieldName\":\"email\",\"type\":\"string\",\"columnName\":\"email\",\"length\":255},\"emailCanonical\":{\"fieldName\":\"emailCanonical\",\"type\":\"string\",\"columnName\":\"email_canonical\",\"length\":255,\"unique\":true},\"enabled\":{\"fieldName\":\"enabled\",\"type\":\"boolean\",\"columnName\":\"enabled\"},\"salt\":{\"fieldName\":\"salt\",\"type\":\"string\",\"columnName\":\"salt\"},\"password\":{\"fieldName\":\"password\",\"type\":\"string\",\"columnName\":\"password\"},\"lastLogin\":{\"fieldName\":\"lastLogin\",\"type\":\"datetime\",\"columnName\":\"last_login\",\"nullable\":true},\"locked\":{\"fieldName\":\"locked\",\"type\":\"boolean\",\"columnName\":\"locked\"},\"expired\":{\"fieldName\":\"expired\",\"type\":\"boolean\",\"columnName\":\"expired\"},\"expiresAt\":{\"fieldName\":\"expiresAt\",\"type\":\"datetime\",\"columnName\":\"expires_at\",\"nullable\":true},\"confirmationToken\":{\"fieldName\":\"confirmationToken\",\"type\":\"string\",\"columnName\":\"confirmation_token\",\"nullable\":true},\"passwordRequestedAt\":{\"fieldName\":\"passwordRequestedAt\",\"type\":\"datetime\",\"columnName\":\"password_requested_at\",\"nullable\":true},\"roles\":{\"fieldName\":\"roles\",\"type\":\"array\",\"columnName\":\"roles\"},\"credentialsExpired\":{\"fieldName\":\"credentialsExpired\",\"type\":\"boolean\",\"columnName\":\"credentials_expired\"},\"credentialsExpireAt\":{\"fieldName\":\"credentialsExpireAt\",\"type\":\"datetime\",\"columnName\":\"credentials_expire_at\",\"nullable\":true}},\"fieldNames\":{\"username\":\"username\",\"username_canonical\":\"usernameCanonical\",\"email\":\"email\",\"email_canonical\":\"emailCanonical\",\"enabled\":\"enabled\",\"salt\":\"salt\",\"password\":\"password\",\"last_login\":\"lastLogin\",\"locked\":\"locked\",\"expired\":\"expired\",\"expires_at\":\"expiresAt\",\"confirmation_token\":\"confirmationToken\",\"password_requested_at\":\"passwordRequestedAt\",\"roles\":\"roles\",\"credentials_expired\":\"credentialsExpired\",\"credentials_expire_at\":\"credentialsExpireAt\"},\"columnNames\":{\"username\":\"username\",\"usernameCanonical\":\"username_canonical\",\"email\":\"email\",\"emailCanonical\":\"email_canonical\",\"enabled\":\"enabled\",\"salt\":\"salt\",\"password\":\"password\",\"lastLogin\":\"last_login\",\"locked\":\"locked\",\"expired\":\"expired\",\"expiresAt\":\"expires_at\",\"confirmationToken\":\"confirmation_token\",\"passwordRequestedAt\":\"password_requested_at\",\"roles\":\"roles\",\"credentialsExpired\":\"credentials_expired\",\"credentialsExpireAt\":\"credentials_expire_at\"},\"discriminatorValue\":null,\"discriminatorMap\":[],\"discriminatorColumn\":null,\"table\":{\"name\":\"User\"},\"lifecycleCallbacks\":[],\"entityListeners\":[],\"associationMappings\":[],\"isIdentifierComposite\":false,\"containsForeignIdentifier\":false,\"idGenerator\":{},\"sequenceGeneratorDefinition\":null,\"tableGeneratorDefinition\":null,\"changeTrackingPolicy\":1,\"isVersioned\":null,\"versionField\":null,\"cache\":null,\"reflClass\":{\"name\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"isReadOnly\":false,\"reflFields\":{\"username\":{\"name\":\"username\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"usernameCanonical\":{\"name\":\"usernameCanonical\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"email\":{\"name\":\"email\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"emailCanonical\":{\"name\":\"emailCanonical\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"enabled\":{\"name\":\"enabled\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"salt\":{\"name\":\"salt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"password\":{\"name\":\"password\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"lastLogin\":{\"name\":\"lastLogin\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"locked\":{\"name\":\"locked\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expired\":{\"name\":\"expired\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"expiresAt\":{\"name\":\"expiresAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"confirmationToken\":{\"name\":\"confirmationToken\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"passwordRequestedAt\":{\"name\":\"passwordRequestedAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"roles\":{\"name\":\"roles\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpired\":{\"name\":\"credentialsExpired\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"},\"credentialsExpireAt\":{\"name\":\"credentialsExpireAt\",\"class\":\"FOS\\\\UserBundle\\\\Model\\\\User\"}}}}');
    window.AdminController.__vars.defaultTables = JSON.parse('{\"ss_user\":{\"id\":[\"id\"],\"name\":[\"first\",\"last\",\"email\"],\"0\":\"groups\",\"packs\":[\"authored\",\"userPacks.pack\"],\"1\":\"roles\",\"actions\":[\"deleted\"]},\"ss_group\":{\"id\":[\"id\",\"name\"],\"name\":[\"logo\",\"userCountStr\",\"descriptionStr\"],\"0\":\"parent\",\"1\":\"invites\",\"packs\":[\"packs\",\"groupPacks\"],\"actions\":[\"deleted\"]},\"pack\":{\"id\":[\"id\"],\"name\":[\"title\",\"logo\",\"userCountStr\",\"cardCountStr\"],\"0\":\"status\",\"1\":[\"cards\",\"group\",\"groups\",\"user\",\"users\",\"userPacks\",\"userPacks.user\"],\"2\":\"properties\",\"3\":\"actions\"},\"card\":{\"id\":[\"id\"],\"name\":[\"type\",\"upload\",\"content\"],\"correct\":[\"correct\",\"answers\"],\"0\":[\"pack\"],\"actions\":[\"deleted\"]},\"invite\":{\"id\":[\"id\",\"code\"],\"name\":[\"first\",\"last\",\"email\",\"created\"],\"actions\":[\"deleted\"]},\"user_pack\":[\"user\",\"pack\",\"removed\",\"downloaded\"],\"file\":{\"id\":[\"id\",\"url\"]}}');
    window.AdminController.createEntity = function (t) {return $.extend({}, window.views.__defaultEntities[t]);};
    window.AdminController.sortByFields = function (arr, fields) {
        arr.sort(function (a, b) { return (a[fields[0]] + ' ' + a[fields[1]]).toLocaleLowerCase() > (b[fields[0]] + ' ' + b[fields[1]]).toLocaleLowerCase() }); }
    window.AdminController.TableMapping = {
        getAssociationMappings: function () { return this.associationMappings; }
    };
    for(var t in window.AdminController.__vars.allTables) {
        if(window.AdminController.__vars.allTables.hasOwnProperty(t)) {
            window.AdminController.__vars.allTables[t] = $.extend(window.AdminController.__vars.allTables[t], window.AdminController.TableMapping);
        }
    }
    window.AdminController.getAllFieldNames = function (tables) { return window.getAllFields(tables); };

    var is_numeric = function (num) {return !isNaN(parseInt(num)) || !isNaN(parseFloat(num));};
    var strlen = function (str) {return (''+(str || '')).length;};
    var array_merge = function () {var args = []; for(var a = 0; a < arguments.length; a++) { args[args.length] = arguments[a]; }; return args.reduce(function (a, b) {return typeof a == 'object' ? $.extend(a, b) : $.merge(a, b);});};
    var trim = function (str) {return (str || '').trim();};
    var explode = function (del, str) {return (str || '').split(del);};
    var array_splice = function (arr, start, length) {return (arr || []).splice(start, length);};
    var array_search = function (item, arr) { var index = (arr || []).indexOf(item); return index == -1 ? false : index; };
    var count = function (arr) { return (arr || []).length; };
    var in_array = function (needle, arr) { return (arr || []).indexOf(needle) > -1; };
    var array_values = function (arr) { return (arr || []).slice(0); };
    var is_array = function (obj) { return typeof obj == 'array' || typeof obj == 'object'; }; // PHP and javascript don't make a distinction between arrays and objects syntax wise using [property], all php objects should be restful anyways
    var array_keys = function (obj) {var result=[]; for (var k in obj) { if (obj.hasOwnProperty(k)) { result[result.length] = k } } return result; };
    var implode = function (sep, arr) {return (arr || []).join(sep);};
    var preg_replace = function (needle, replacement, subject) {debugger; return (subject || '').replace(new RegExp(needle.split('/').slice(1, -1).join('/'), needle.split('/').slice(-1)[0]), replacement);};
    var ucfirst = function (str) {return (str || '').substr(0, 1).toLocaleUpperCase() + str.substr(1);};
    var str_replace = function (needle, replacement, haystack) {return (haystack || '').replace(new RegExp(RegExp.escape(needle), 'g'), replacement);};
    var call_user_func_array = function (context, params) {return context[context[1]].apply(context[0], params);};
    var print = function (s) { window.views.__output += s };
    var strtolower = function(s) { return s.toLocaleLowerCase(); };
    var empty = function(s) { return typeof s == 'undefined' || ('' + s).trim() == '' || s == false || s == null; };
    var json_encode = JSON.stringify;
    var method_exists = function (s,m) { return typeof s == 'object' && typeof s[m] == 'function'; };
    var isset = function (s) { return typeof s != 'undefined'; };



//-----------------------------------------------------------cell_title-----------------------------------------------------------

    window.views['cell_title'] = ( function cell_title (__vars) {print('');
        for (__vars.f in __vars.fields) {
            if(!__vars.fields.hasOwnProperty(__vars.f)) { continue; }
            __vars.field = __vars.fields[__vars.f];
            if(method_exists(__vars.entity, __vars.method = 'get' . ucfirst(__vars.field))) {
                __vars.fields[__vars.f] = __vars.entity.__vars.method();
            }
        }
        print (__vars.view.render('AdminBundle:Admin:cell-label.html.php', {'fields' : __vars.fields}));
    });



//-----------------------------------------------------------header_packPacks-----------------------------------------------------------

    window.views['header_packPacks'] = ( function header_packPacks (__vars) {print('<header class="pack">' + "\n"
        + '    <label>Pack name</label>' + "\n"
        + '    <label>Pack status</label>' + "\n"
        + '    <label>Keyboard type</label>' + "\n"
        + '</header>');
    });



//-----------------------------------------------------------cell_subgroup_ss_group-----------------------------------------------------------

    window.views['cell_subgroup_ss_group'] = ( function cell_subgroup_ss_group (__vars) {print('<a href="'); print (__vars.view['router'].generate('packs_new')); print('" class="big-add">Add <span>+</span> new subgroup</a>');
    });



//-----------------------------------------------------------header-----------------------------------------------------------

    window.views['header'] = ( function header (__vars) {print('<h2 class="'); print (__vars.table); print('"><a' + "\n"
        + '        name="'); print (__vars.table); print('">'); print (ucfirst(str_replace('ss_', '', __vars.table))); print('s</a> <a' + "\n"
        + '        href="#add-'); print (__vars.table); print('">+</a>' + "\n"
        + '    <small>('); print (__vars.results[implode('', [__vars.table , '_total'])]); print(')</small>' + "\n"
        + '</h2>');});



//-----------------------------------------------------------upload_file-----------------------------------------------------------

    window.views['upload_file'] = ( function upload_file (__vars) {print(''); __vars.view.extend('AdminBundle:Admin:dialog.html.php');

        __vars.view['slots'].start('modal-header'); print('' + "\n"
            + '<h3>Upload an image</h3>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('modal-body'); print('' + "\n"
            + '<div class="plupload">' + "\n"
            + '    <div class="plup-filelist">' + "\n"
            + '        <img width="300" height="100" src="'); print (__vars.view.escape(__vars.view['assets'].getUrl('bundles/studysauce/images/upload_all.png'))); print('" alt="Upload" class="centerized default" />' + "\n"
            + '        <a href="#file-select" class="plup-select" id="file-upload-select">Drag image here or click to select (1GB max)</a>' + "\n"
            + '    </div>' + "\n"
            + '    <input type="hidden" name="">' + "\n"
            + '</div>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('modal-footer'); print('' + "\n"
            + '<a href="#close" class="btn" data-dismiss="modal">Cancel</a>' + "\n"
            + '<a href="#submit-upload" class="btn btn-primary" data-dismiss="modal">Save</a>' + "\n"
            + ''); __vars.view['slots'].stop(); print('');
    });



//-----------------------------------------------------------cell_label-----------------------------------------------------------

    window.views['cell_label'] = ( function cell_label (__vars) {print('');
        for (__vars.f in __vars.fields) {
            if(!__vars.fields.hasOwnProperty(__vars.f)) { continue; }
            __vars.field = __vars.fields[__vars.f]; print('' + "\n"
                + '<label>' + "\n"
                + '    <span>'); print (__vars.view.escape(__vars.field)); print('</span>' + "\n"
                + '</label>' + "\n"
                + ''); }});



//-----------------------------------------------------------cell_cardMastery_pack-----------------------------------------------------------

    window.views['cell_cardMastery_pack'] = ( function cell_cardMastery_pack (__vars) {print('');});



//-----------------------------------------------------------heading_actions-----------------------------------------------------------

    window.views['heading_actions'] = ( function heading_actions (__vars) {print('<label><select name="actions">' + "\n"
        + '        <option value="">Select All</option>' + "\n"
        + '        <option value="delete">Delete All</option>' + "\n"
        + '    </select></label>');});



//-----------------------------------------------------------cell_id_ss_group-----------------------------------------------------------

    window.views['cell_id_ss_group'] = ( function cell_id_ss_group (__vars) {print('');




        /** @var Group __vars.ss_group */
        __vars.time = method_exists(__vars.ss_group, 'getModified') && !empty(__vars.ss_group.getModified()) ? __vars.ss_group.getModified() : __vars.ss_group.getCreated();

        if (empty(__vars.ss_group.getLogo())) { print('' + "\n"
            + '        <img width="300" height="100" src="'); print (__vars.view.escape(__vars.view['assets'].getUrl('bundles/studysauce/images/upload_image.png'))); print('" class="default centerized" alt="Upload"/>' + "\n"
            + '    ');
        } else { print('<img height="50" src="'); print (__vars.ss_group.getLogo().getUrl()); print('" class="centerized" />'); } print('');
    });



//-----------------------------------------------------------cell_expandMembers_ss_group-----------------------------------------------------------

    window.views['cell_expandMembers_ss_group'] = ( function cell_expandMembers_ss_group (__vars) {print('');





        /** @var Group __vars.ss_group */

        __vars.entityIds = [];
        /** @var User[] __vars.users */
        __vars.users = __vars.ss_group.getUsers().toArray();
        AdminController.sortByFields(__vars.users, ['first', 'last']);
        __vars.ids = [];
        __vars.removed = [];
        for (__vars.for___3 in __vars.users) {
            if(!__vars.users.hasOwnProperty(__vars.for___3)) { continue; }
            __vars.u = __vars.users[__vars.for___3];
            __vars.ids[count(__vars.ids)] = implode('', ['ss_user-' , __vars.u.getId()]);
            if(!empty(__vars.request['pack-id']) && !empty(__vars.up = __vars.u.getUserPack(__vars.results['pack'][0])) && __vars.up.getRemoved()) {
                __vars.removed[count(__vars.removed)] = __vars.u;
            }
        }
        /** @var Pack[] __vars.packs */
        __vars.packs = __vars.ss_group.getPacks().toArray();
        AdminController.sortByFields(__vars.packs, ['title']);
        __vars.packIds = [];
        for (__vars.for___4 in __vars.packs) {
            if(!__vars.packs.hasOwnProperty(__vars.for___4)) { continue; }
            __vars.p = __vars.packs[__vars.for___4];
            __vars.packIds[count(__vars.packIds)] = implode('', ['pack-' , __vars.p.getId()]);
        }
        print('' + "\n"
            + '<form action="'); print (__vars.view['router'].generate('save_group', {'ss_group' : {'id' : __vars.ss_group.getId()}, 'tables' : {'ss_group' : ['users']}})); print('">' + "\n"
            + '' + "\n"
            + '    ');
        __vars.groupMembersList = {
            'tables' : {'ss_user' : AdminController.__vars.defaultMiniTables['ss_user']},
            'entities' : __vars.users,
            'entityIds' : __vars.ids,
            'fieldName' : 'ss_group[users]'};

        // TODO: add field name
        if(!isset(__vars.request['pack-id']) || empty(__vars.request['pack-id'])) {
            if((!isset(__vars.request['parent-ss_group-id'])
                || __vars.ss_group.getId() != __vars.request['parent-ss_group-id'])) {
                print (__vars.view.render('AdminBundle:Admin:cell-collection.html.php', {
                    'tables' : {'pack' : AdminController.__vars.defaultMiniTables['pack']},
                    'entities' : __vars.packs,
                    'entityIds' : __vars.packIds,
                    'fieldName' : 'ss_group[packs]'}));
            }
        }
        else {
            __vars.groupMembersList['removedEntities'] = __vars.removed;
        }
        print('' + "\n"
            + '' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cell-collection.html.php', __vars.groupMembersList)); print('' + "\n"
            + '</form>');
    });



//-----------------------------------------------------------cell_id-----------------------------------------------------------

    window.views['cell_id'] = ( function cell_id (__vars) {print('');



        /** @var User|Group __vars.entity */
        __vars.time = method_exists(__vars.entity, 'getModified') && !empty(__vars.entity.getModified()) ? __vars.entity.getModified() : __vars.entity.getCreated();
        print('' + "\n"
            + '<div data-timestamp="'); print (empty(__vars.time) ? 0 : __vars.time.getTimestamp()); print('">'); print (empty(__vars.time) ? '' : __vars.time.format('j M H:i')); print('</div>');
    });



//-----------------------------------------------------------header_groupGroups-----------------------------------------------------------

    window.views['header_groupGroups'] = ( function header_groupGroups (__vars) {print('<header class="ss_group">' + "\n"
        + '    <label>Group name</label>' + "\n"
        + '    <label>Parent group</label>' + "\n"
        + '    <label>Invite code</label>' + "\n"
        + '</header>');});



//-----------------------------------------------------------cell_name_ss_user-----------------------------------------------------------

    window.views['cell_name_ss_user'] = ( function cell_name_ss_user (__vars) {print('');


        /** @var User __vars.ss_user */
        print('' + "\n"
            + '' + "\n"
            + '<div class="user-name">' + "\n"
            + '    <label class="input first"><input type="text" name="first"' + "\n"
            + '                                value="'); print (__vars.ss_user.getFirst()); print('"' + "\n"
            + '                                placeholder="First name"/></label>' + "\n"
            + '    <label class="input last"><input type="text" name="last"' + "\n"
            + '                                value="'); print (__vars.ss_user.getLast()); print('"' + "\n"
            + '                                placeholder="Last name"/></label>' + "\n"
            + '    <label class="input email"><input type="text" name="email"' + "\n"
            + '                                value="'); print (__vars.ss_user.getEmail()); print('"' + "\n"
            + '                                placeholder="Email"/></label>' + "\n"
            + '</div>');
    });



//-----------------------------------------------------------heading_actions_ss_user-----------------------------------------------------------

    window.views['heading_actions_ss_user'] = ( function heading_actions_ss_user (__vars) {print('<label><select name="actions">' + "\n"
        + '        <option value="">Select All</option>' + "\n"
        + '        <option value="delete">Delete All</option>' + "\n"
        + '        <option value="cancel">Cancel All</option>' + "\n"
        + '        <option value="email">Email All</option>' + "\n"
        + '        <option value="export">Export All</option>' + "\n"
        + '        <option value="export">Clear All</option>' + "\n"
        + '    </select></label>');});



//-----------------------------------------------------------groups-----------------------------------------------------------

    window.views['groups'] = ( function groups (__vars) {print('');











        /** @var GlobalVariables __vars.app */
        /** @var __vars.view TimedPhpEngine */
        /** @var Group __vars.entity */

        __vars.view.extend('StudySauceBundle:Shared:dashboard.html.php');

        __vars.view['slots'].start('stylesheets');
        for (__vars.for___5 in __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'}).hasOwnProperty(__vars.for___5)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})[__vars.for___5]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        for (__vars.for___6 in __vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], {'output' : 'bundles/studysauce/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], {'output' : 'bundles/studysauce/css/*.css'}).hasOwnProperty(__vars.for___6)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], {'output' : 'bundles/studysauce/css/*.css'})[__vars.for___6]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        __vars.view['slots'].stop();

        __vars.view['slots'].start('javascripts');
        for (__vars.for___7 in __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'}).hasOwnProperty(__vars.for___7)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})[__vars.for___7]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); }
        for (__vars.for___8 in __vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], {'output' : 'bundles/studysauce/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], {'output' : 'bundles/studysauce/js/*.js'}).hasOwnProperty(__vars.for___8)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], {'output' : 'bundles/studysauce/js/*.js'})[__vars.for___8]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); }
        __vars.view['slots'].stop();

        __vars.view['slots'].start('body'); print('' + "\n"
            + '    <div class="panel-pane ');
        print (!empty(__vars.entity) && __vars.entity.getSubgroups().count() > 0 ? ' has-subgroups' : ''); print('"' + "\n"
            + '        id="groups'); print (__vars.entity !== null ? implode('', ['-group' , intval(__vars.entity.getId())]) : ''); print('">' + "\n"
            + '        <div class="pane-content">' + "\n"
            + '            '); if (__vars.entity !== null) { print('' + "\n"
            + '                <form action="'); print (__vars.view['router'].generate('save_group')); print('" class="group-edit">' + "\n"
            + '                    ');
            __vars.tables = {
                'ss_group' : {'idEdit' : ['created', 'id', 'logo'], 'name' : ['name', 'description'], 'parent' : [], 'invite' : ['invites'], 'actions' : ['deleted']}
            };
            __vars.isNew = empty(__vars.entity.getId());
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', {
                'count-ss_group' : 1,
                'ss_group-deleted' : __vars.entity.getDeleted(),
                'edit' : !__vars.isNew ? false : ['ss_group'],
                'read-only' : __vars.isNew ? false : ['ss_group'],
                'new' : __vars.isNew,
                'ss_group-id' : __vars.entity.getId(),
                'tables' : __vars.tables,
                'headers' : {'ss_group' : 'groupGroups'},
                'footers' : {'ss_group' : 'groupGroups'}
            })));
            print('' + "\n"
                + '                </form>' + "\n"
                + '            '); } print('' + "\n"
            + '            <div class="membership">' + "\n"
            + '                <div class="group-list">' + "\n"
            + '                    ');
        __vars.tiles = {'ss_group' : {'idTiles' : ['created', 'id', 'name', 'userCountStr', 'descriptionStr'], 'packList' : ['groupPacks', 'parent'], 'actions' : ['deleted']}};
        if (empty(__vars.entity)) {
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', {
                'tables' : __vars.tiles,
                'parent-ss_group-id' : 'NULL',
                'count-ss_group' : 0,
                'classes' : ['tiles'],
                'headers' : {'ss_group' : 'newGroup'},
                'footers' : {'ss_group' : 'newGroup'}
            })));
        } else {
            // TODO: check view setting
            __vars.tableViews = {};
            __vars.tableViews['Tiles'] = {};
            __vars.tableViews['Tiles'] = {
                'tables' : __vars.tiles,
                'classes' : ['tiles'],
            };
            __vars.tableViews['Membership'] = {};
            __vars.tableViews['Membership']['tables'] = {};
            __vars.tableViews['Membership']['tables']['ss_group-1'] = {'0' : 'id', '1' : 'title', 'expandMembers' : ['deleted'] /* search field but don't display a template */};
            __vars.tableViews['Membership']['tables']['ss_group'] = {'0' : 'id', '1' : 'title', 'expandMembers' : ['parent'], 'actions' : ['deleted'] /* search field but don't display a template */};
            __vars.tableViews['Membership']['classes'] = ['last-right-expand'];
            __vars.tableView = __vars.tableViews[empty(__vars.app.getRequest().get('view')) || __vars.app.getRequest().get('view') != 'Tiles' ? 'Membership' : 'Tiles'];
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', array_merge(__vars.tableView, {
                'ss_group-1headers' : {'ss_group' : 'subGroups'},
                'ss_group-1footers' : false,
                'ss_group-1ss_group-id' : !empty(__vars.entity.getId()) ? __vars.entity.getId() : '0',
                'parent-ss_group-id' : !empty(__vars.entity.getId()) ? __vars.entity.getId() : '0',
                'count-ss_group' : 0,
                'ss_group-deleted' : __vars.entity.getDeleted(),
                'edit' : false,
                'read-only' : false,
                'headers' : false,
                'footers' : {'ss_group' : 'groupCount'},
                'views' : __vars.tableViews
            }))));
        } print('' + "\n"
            + '                </div>' + "\n"
            + '                '); if (!empty(__vars.entity)) { print('' + "\n"
            + '                <div class="list-packs">' + "\n"
            + '                    ');
            __vars.tables = {'ss_group' : ['id', 'deleted'], 'ss_user' : ['first', 'last', 'email', 'id', 'deleted', 'userPacks', 'groups'], 'user_pack' : ['user', 'pack', 'removed', 'downloaded'], 'card' : ['id', 'deleted']};
            __vars.tables['pack'] = {'0' : 'id', 'title' : ['title', 'logo', 'cards'], 'expandMembers' : ['group', 'groups', 'users', 'userPacks'], 'actionsGroup' : ['status'] /* search field but don't display a template */};
            __vars.isNew = empty(__vars.entity.getId());
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', {
                'count-pack' : __vars.isNew ? -1 : 0,
                'count-ss_group' : 1,
                'count-card' : -1,
                'count-ss_user' : -1,
                'count-user_pack' : -1,
                'ss_group-deleted' : __vars.entity.getDeleted(),
                'edit' : false,
                'classes' : ['last-right-expand'],
                'read-only' : false,
                'ss_group-id' : __vars.entity.getId(),
                'tables' : __vars.tables,
                'headers' : {'ss_group' : 'groupPacks'},
                'footers' : {'pack' : 'groupPacks'}
            })));
            print('' + "\n"
                + '                </div>' + "\n"
                + '                <div class="empty-members">' + "\n"
                + '                    <div>Select name on the left to see group members</div>' + "\n"
                + '                </div>' + "\n"
                + '                '); } print('' + "\n"
            + '            </div>' + "\n"
            + '        </div>' + "\n"
            + '    </div>' + "\n"
            + ''); __vars.view['slots'].stop(); print('' + "\n"
            + '' + "\n"
            + ''); __vars.view['slots'].start('sincludes');
        __vars.view['slots'].stop();

    });



//-----------------------------------------------------------cell_id_ss_user-----------------------------------------------------------

    window.views['cell_id_ss_user'] = ( function cell_id_ss_user (__vars) {print('');



        /** @var User __vars.ss_user */
        __vars.time = !empty(__vars.ss_user.getLastVisit()) ? __vars.ss_user.getLastVisit() : __vars.ss_user.getCreated();
        print('' + "\n"
            + '<div data-timestamp="'); print (!empty(__vars.time) ? __vars.time.getTimestamp() : 0); print('">'); print (!empty(__vars.time) ? __vars.time.format('j M H:i') : ''); print('</div>');
    });



//-----------------------------------------------------------create_entity-----------------------------------------------------------

    window.views['create_entity'] = ( function create_entity (__vars) {print('');

        __vars.view.extend('AdminBundle:Admin:dialog.html.php', {'id' : 'create-entity'});

        __vars.context = jQuery(__vars.this);
        __vars.dialog = __vars.context.find('#create-entity');

        __vars.tableName = array_keys(__vars.tables)[0];

        __vars.view['slots'].start('modal-header'); print('' + "\n"
            + '    <h3>Create a new '); print (ucfirst(str_replace('ss_', '', __vars.tableName))); print('</h3>' + "\n"
            + ''); __vars.view['slots'].stop();

        if(__vars.tableName == 'pack') {
            __vars.newPath = __vars.view['router'].generate('packs_new');
        }
        if(__vars.tableName == 'ss_group') {
            __vars.newPath = __vars.view['router'].generate('groups_new');
        }

        __vars.view['slots'].start('modal-body'); print('' + "\n"
            + '    <a href="'); print (__vars.newPath); print('" class="cloak"><span class="reveal">Start from scratch</span></a>' + "\n"
            + '    <a href="#add-entity" data-target="#add-entity" data-toggle="modal" class="cloak"><span class="reveal">Find existing '); print (str_replace('ss_', '', __vars.tableName)); print('s</span></a>' + "\n"
            + ''); __vars.view['slots'].stop();

        if(__vars.dialog.length > 0) {
            __vars.dialog.find('h3').remove();
            __vars.dialog.find('.modal-header').append(jQuery(__vars.view['slots'].get('modal-header')));
            __vars.dialog.find('a').remove();
            __vars.dialog.find('.modal-body').append(jQuery(__vars.view['slots'].get('modal-body')));
        }

    });



//-----------------------------------------------------------header_basic-----------------------------------------------------------

    window.views['header_basic'] = ( function header_basic (__vars) {print('<header class="'); print (__vars.table); print('">' + "\n"
        + '    ');
        __vars.templates = []; // template name => classes
        // TODO: build backwards so its right aligned when there are different field counts
        for (__vars.i = 0; __vars.i < count(__vars.tables[__vars.table]); __vars.i++) {
            __vars.field = is_array(array_values(__vars.tables[__vars.table])[__vars.i]) ? array_keys(__vars.tables[__vars.table])[__vars.i] : array_values(__vars.tables[__vars.table])[__vars.i];
            // skip search only fields
            if(is_numeric(__vars.field)) {
                continue;
            }
            print('' + "\n"
                + '        <label class="'); print (__vars.field); print('">' + "\n"
                + '            '); print (__vars.view.render('AdminBundle:Admin:heading.html.php', {'groups' : __vars.allGroups, 'field' : __vars.field})); print('' + "\n"
                + '        </label>' + "\n"
                + '        ');
        } print('' + "\n"
            + '</header>');});



//-----------------------------------------------------------heading_properties_pack-----------------------------------------------------------

    window.views['heading_properties_pack'] = ( function heading_properties_pack (__vars) {print('<label class="input">' + "\n"
        + '    <select name="properties">' + "\n"
        + '        <option value="">Properties</option>' + "\n"
        + '        <option value="UNPUBLISHED">Keyboard</option>' + "\n"
        + '    </select>' + "\n"
        + '</label>');});



//-----------------------------------------------------------cells-----------------------------------------------------------

    window.views['cells'] = ( function cells (__vars) {print('');
        for (__vars.f in __vars.tables[__vars.table]) {
            if(!__vars.tables[__vars.table].hasOwnProperty(__vars.f)) { continue; }
            __vars.fields = __vars.tables[__vars.table][__vars.f];
            __vars.field = is_array(__vars.fields) ? __vars.f : __vars.fields;
            // skip search only fields
            if(is_numeric(__vars.field)) {
                continue;
            }
            print('' + "\n"
                + '<div class="'); print (__vars.field); print('">' + "\n"
                + '    ');
            if (__vars.view.exists(implode('', ['AdminBundle:Admin:cell-' , __vars.field , '-' , __vars.table , '.html.php']))) {
                __vars.specificCell = {
                    'groups' : __vars.allGroups,
                    'table' : __vars.table,
                    'request' : __vars.request,
                    'results' : __vars.results};
                __vars.specificCell[__vars.table] = __vars.entity;
                print (__vars.view.render(implode('', ['AdminBundle:Admin:cell-' , __vars.field , '-' , __vars.table , '.html.php']), __vars.specificCell));
            } else if (__vars.view.exists(implode('', ['AdminBundle:Admin:cell-' , __vars.field , '.html.php']))) {
                print (__vars.view.render(implode('', ['AdminBundle:Admin:cell-' , __vars.field , '.html.php']), {
                    'entity' : __vars.entity,
                    'groups' : __vars.allGroups,
                    'table' : __vars.table,
                    'request' : __vars.request,
                    'results' : __vars.results}));
            } else {
                print (__vars.view.render('AdminBundle:Admin:cell-generic.html.php', {
                    'tables' : __vars.tables,
                    'fields' : is_array(__vars.fields) ? __vars.fields : [__vars.fields],
                    'field' : __vars.field,
                    'entity' : __vars.entity,
                    'groups' : __vars.allGroups,
                    'table' : __vars.table,
                    'request' : __vars.request,
                    'results' : __vars.results}));
            }
            print('</div>' + "\n"
                + '' + "\n"
                + '    ');
        }
    });



//-----------------------------------------------------------heading_groups-----------------------------------------------------------

    window.views['heading_groups'] = ( function heading_groups (__vars) {print('<label class="input">' + "\n"
        + '    <select name="ss_group-id">' + "\n"
        + '        <option value="">Group</option>' + "\n"
        + '        <option value="_ascending">Ascending (A-Z)</option>' + "\n"
        + '        <option value="_descending">Descending (Z-A)</option>' + "\n"
        + '        ');

        for (__vars.i in __vars.groups) {
            if(!__vars.groups.hasOwnProperty(__vars.i)) { continue; }
            __vars.g = __vars.groups[__vars.i];
            /** @var Group __vars.g */
            print('' + "\n"
                + '            <option' + "\n"
                + '            value="'); print (__vars.g.getId()); print('">'); print (__vars.g.getName()); print('</option>');
        } print('' + "\n"
            + '        <option value="nogroup">No Groups</option>' + "\n"
            + '    </select></label>');});



//-----------------------------------------------------------row-----------------------------------------------------------

    window.views['row'] = ( function row (__vars) {print('');




        /** @var GlobalVariables __vars.app */

        /** @var User|Group __vars.entity */

        __vars.rowId = implode('', [__vars.table , '-id-']);
        if(method_exists(__vars.entity, 'getId')) {
            __vars.rowId = implode('', [__vars.rowId , __vars.entity.getId()]);
        }

        __vars.expandable = isset(__vars.request['expandable']) && is_array(__vars.request['expandable'])
            ? __vars.request['expandable']
            : [];


        print('' + "\n"
            + '<div class="'); print (__vars.table); print('-row ');
        print (__vars.rowId); print(' ');
        print (isset(__vars.request['edit']) && (__vars.request['edit'] === true
        || is_array(__vars.request['edit']) && in_array(__vars.table, __vars.request['edit']))
            ? 'edit'
            : (isset(__vars.request['read-only']) && (__vars.request['read-only'] === false
        || is_array(__vars.request['read-only']) && !in_array(__vars.table, __vars.request['read-only']))
            ? ''
            : 'read-only')); print(' ');
        print (isset(__vars.expandable[__vars.table]) ? 'expandable' : ''); print(' ');
        print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cells.html.php', {
            'entity' : __vars.entity,
            'tables' : __vars.tables,
            'table' : __vars.table,
            'allGroups' : __vars.allGroups,
            'request' : __vars.request,
            'results' : __vars.results})); print('' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>' + "\n"
            + '</div>' + "\n"
            + ''); if (isset(__vars.expandable[__vars.table])) { print('' + "\n"
            + '    <div class="expandable ');
            print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
                + '    '); print (__vars.view.render('AdminBundle:Admin:cells.html.php', {
                'entity' : __vars.entity,
                'tables' : __vars.expandable,
                'table' : __vars.table,
                'allGroups' : __vars.allGroups,
                'request' : __vars.request,
                'results' : __vars.results})); print('' + "\n"
                + '    </div>');
        }});



//-----------------------------------------------------------cell_parent_ss_group-----------------------------------------------------------

    window.views['cell_parent_ss_group'] = ( function cell_parent_ss_group (__vars) {print('');

        /** @var Group __vars.ss_group */
        print('' + "\n"
            + '<label>' + "\n"
            + '    <select name="parent">' + "\n"
            + '        <option value="'); print (__vars.ss_group.getId()); print('" '); print (empty(__vars.ss_group.getParent()) ? 'selected="selected"' : ''); print('>No parent</option>' + "\n"
            + '        ');
        __vars.topGroups = [];
        for (__vars.for___11 in __vars.groups) {
            if(!__vars.groups.hasOwnProperty(__vars.for___11)) { continue; }
            __vars.g = __vars.groups[__vars.for___11];
            /** @var Group __vars.g */
            if(empty(__vars.g.getParent())) {
                __vars.topGroups[count(__vars.topGroups)] = __vars.g;
            }
        }
        print (__vars.view.render('AdminBundle:Admin:cell-parentOptions-ss_group', {'groups' : __vars.topGroups})); print('' + "\n"
            + '    </select>' + "\n"
            + '</label>');
    });



//-----------------------------------------------------------cell_actions_ss_group-----------------------------------------------------------

    window.views['cell_actions_ss_group'] = ( function cell_actions_ss_group (__vars) {print('' + "\n"
        + '' + "\n"
        + '<div class="highlighted-link">' + "\n"
        + '    ');
        if(isset(__vars.request['pack-id']) && !empty(__vars.packId = __vars.request['pack-id'])) { print('' + "\n"
            + '        <a href="#general-dialog" data-confirm="Are you sure you would like to remove the group &ldquo;'); print (__vars.ss_group.getName()); print('&rdquo; from the pack?" class="remove-icon" data-action="'); print (__vars.view['router'].generate('save_group', {'ss_group' : {'id' : __vars.ss_group.getId(), 'groupPacks' : {'id' : __vars.packId, 'remove' : 'true'}}})); print('" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>' + "\n"
            + '    '); }
        else { print('' + "\n"
            + '        <a href="#general-dialog" data-confirm="Are you sure you would like to delete the group &ldquo;'); print (__vars.ss_group.getName()); print('&rdquo; permanently?" class="remove-icon" data-action="'); print (__vars.view['router'].generate('save_group', {'ss_group' : {'id' : __vars.ss_group.getId(), 'deleted' : true}, 'tables' : {'ss_group' : ['name', 'deleted']}})); print('" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>' + "\n"
            + '    '); } print('' + "\n"
            + '</div>');
    });



//-----------------------------------------------------------cell_actions_pack-----------------------------------------------------------

    window.views['cell_actions_pack'] = ( function cell_actions_pack (__vars) {print('<div class="highlighted-link">' + "\n"
        + '    <a title="Remove pack" href="#general-dialog" data-confirm="Are you sure you would like to delete the pack &ldquo;'); print (__vars.pack.getTitle()); print('&rdquo; permanently?" class="remove-icon" data-action="'); print (__vars.view['router'].generate('command_save', {'pack' : {'id' : __vars.pack.getId(), 'status' : 'DELETED'}, 'tables' : {'pack' : 'status'}, 'redirect' : __vars.view['router'].generate('packs')})); print('" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------heading_name-----------------------------------------------------------

    window.views['heading_name'] = ( function heading_name (__vars) {print('<label class="input">' + "\n"
        + '    <select name="name">' + "\n"
        + '        <option value="">Name</option>' + "\n"
        + '        <option value="_ascending">Ascending (A-Z)</option>' + "\n"
        + '        <option value="_descending">Descending (Z-A)</option>' + "\n"
        + '        <option value="A%">A</option>' + "\n"
        + '        <option value="B%">B</option>' + "\n"
        + '        <option value="C%">C</option>' + "\n"
        + '        <option value="D%">D</option>' + "\n"
        + '        <option value="E%">E</option>' + "\n"
        + '        <option value="F%">F</option>' + "\n"
        + '        <option value="G%">G</option>' + "\n"
        + '        <option value="H%">H</option>' + "\n"
        + '        <option value="I%">I</option>' + "\n"
        + '        <option value="J%">J</option>' + "\n"
        + '        <option value="K%">K</option>' + "\n"
        + '        <option value="L%">L</option>' + "\n"
        + '        <option value="M%">M</option>' + "\n"
        + '        <option value="N%">N</option>' + "\n"
        + '        <option value="O%">O</option>' + "\n"
        + '        <option value="P%">P</option>' + "\n"
        + '        <option value="Q%">Q</option>' + "\n"
        + '        <option value="R%">R</option>' + "\n"
        + '        <option value="S%">S</option>' + "\n"
        + '        <option value="T%">T</option>' + "\n"
        + '        <option value="U%">U</option>' + "\n"
        + '        <option value="V%">V</option>' + "\n"
        + '        <option value="W%">W</option>' + "\n"
        + '        <option value="X%">X</option>' + "\n"
        + '        <option value="Y%">Y</option>' + "\n"
        + '        <option value="Z%">Z</option>' + "\n"
        + '    </select></label>');});



//-----------------------------------------------------------cell_name_ss_group-----------------------------------------------------------

    window.views['cell_name_ss_group'] = ( function cell_name_ss_group (__vars) {print('');



        /** @var Group __vars.ss_group */
        print('' + "\n"
            + '<label class="input">' + "\n"
            + '    <input type="text" name="name" value="'); print (__vars.view.escape(__vars.ss_group.getName())); print('"/>' + "\n"
            + '</label>');
    });



//-----------------------------------------------------------header_newPack-----------------------------------------------------------

    window.views['header_newPack'] = ( function header_newPack (__vars) {print('<header class="'); print (__vars.table); print('">' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('packs_new')); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> new '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '</header>');});



//-----------------------------------------------------------heading_users-----------------------------------------------------------

    window.views['heading_users'] = ( function heading_users (__vars) {print('<label class="input">' + "\n"
        + '    <input type="text" name="users" value="" placeholder="Any User / Id"/>' + "\n"
        + '</label>');});



//-----------------------------------------------------------cell_title_ss_group-----------------------------------------------------------

    window.views['cell_title_ss_group'] = ( function cell_title_ss_group (__vars) {print('');





        /** @var Group __vars.ss_group */
        __vars.usersGroupsPacks = __vars.ss_group.getUsersPacksGroupsRecursively();
        __vars.users = __vars.usersGroupsPacks[0];
        __vars.packs = __vars.usersGroupsPacks[1];

        if (isset(__vars.request['parent-ss_group-id']) && __vars.ss_group.getId() == __vars.request['parent-ss_group-id']) {
            print (__vars.view.render('AdminBundle:Admin:cell-label.html.php', {'fields' : ['All users (not in subgroups below)', 0, 0]}));
        } else { print('' + "\n"
            + '    <a href="'); print (__vars.view['router'].generate('groups_edit', {'group' : __vars.ss_group.getId()})); print('">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cell-label.html.php', {'fields' : [__vars.ss_group.getName(), count(__vars.users), count(__vars.packs)]})); print('' + "\n"
            + '    </a>' + "\n"
            + ''); }});



//-----------------------------------------------------------cell_actions-----------------------------------------------------------

    window.views['cell_actions'] = ( function cell_actions (__vars) {print('<div class="highlighted-link">' + "\n"
        + '    <a title="Edit '); print (__vars.table); print('" href="#edit-'); print (__vars.table); print('"></a>' + "\n"
        + '    <a title="Remove '); print (__vars.table); print('" href="#remove-'); print (__vars.table); print('"></a>' + "\n"
        + '    <a href="#cancel-edit">Cancel</a>' + "\n"
        + '    <button type="submit" value="#save-'); print (__vars.table); print('" class="more">Save</button>' + "\n"
        + '</div>');
    });



//-----------------------------------------------------------cell_collection-----------------------------------------------------------

    window.views['cell_collection'] = ( function cell_collection (__vars) {print('');




        __vars.tableNames = array_keys(__vars.tables);

        __vars.view['slots'].start('cell-collection-create'); print('' + "\n"
            + '    <div class="entity-search '); print (implode(' ', __vars.tableNames)); print('">' + "\n"
            + '        <label class="input">' + "\n"
            + '            <input type="text" name="'); print (isset(__vars.fieldName) ? __vars.fieldName : implode('_', __vars.tableNames)); print('" value=""' + "\n"
            + '                   data-confirm="'); print (!isset(__vars.dataConfirm) || __vars.dataConfirm ? 'true' : 'false'); print('" /></label>' + "\n"
            + '    </div>' + "\n"
            + ''); __vars.view['slots'].stop();


        __vars.view['slots'].start('cell-collection-header'); print('' + "\n"
            + '    <header>' + "\n"
            + '        <label></label>' + "\n"
            + '        <a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add' + "\n"
            + '            <span>+</span> individual</a>' + "\n"
            + '    </header>' + "\n"
            + ''); __vars.view['slots'].stop();



        __vars.context = !empty(__vars.context) ? __vars.context : jQuery(__vars.this);
        __vars.search = __vars.context.find('.entity-search');
        if(__vars.search.length == 0) {
            __vars.search = __vars.context.append(__vars.view['slots'].get('cell-collection-create')).find('.entity-search');
            __vars.input = __vars.search.find('.input > input');
        }
        else {
            __vars.input = __vars.search.find('.input > input');
        }

        if (isset(__vars.entities) && (!isset(__vars.inline) || __vars.inline !== true)) {
            if (__vars.search.find('header:not(.removed)').length == 0) {
                jQuery(__vars.view['slots'].get('cell-collection-header')).insertBefore(__vars.search.find('.input'));
            }
        }

        __vars.entityIds = isset(__vars.entityIds) && is_array(__vars.entityIds) ? __vars.entityIds : [];
        __vars.listIds = [];
        __vars.dataTypes = {}; // TODO: fix this syntax in JS
        __vars.removedEntities = isset(__vars.removedEntities) && is_array(__vars.removedEntities) ? __vars.removedEntities : [];
        if (isset(__vars.entities)) {
            for (__vars.for___12 in __vars.entities) {
                if(!__vars.entities.hasOwnProperty(__vars.for___12)) { continue; }
                __vars.entity = __vars.entities[__vars.for___12];
                __vars.dataEntity = AdminController.toFirewalledEntityArray(__vars.entity, __vars.tables);
                if(in_array(__vars.entity, __vars.removedEntities)) {
                    __vars.dataEntity['removed'] = true;
                }
                if(!isset(__vars.dataEntity['removed'])) {
                    __vars.dataEntity['removed'] = false;
                }
                __vars.key = implode('', [__vars.dataEntity['table'] , '-' , __vars.dataEntity['id']]);
                __vars.table = __vars.dataEntity['table'];
                __vars.listIds[count(__vars.listIds)] = __vars.key;
                __vars.unsetId = array_search(__vars.key, __vars.entityIds);
                if (!__vars.dataEntity['removed'] && __vars.unsetId === false) {
                    __vars.entityIds[count(__vars.entityIds)] = __vars.key;
                }
                else if (__vars.dataEntity['removed'] && __vars.unsetId !== false) {
                    array_splice(__vars.entityIds, __vars.unsetId, 1);
                }
                if(!isset(__vars.dataTypes[__vars.table])) {
                    __vars.dataTypes[__vars.table] = [];
                }
                __vars.dataTypes[__vars.table][count(__vars.dataTypes[__vars.table])] = __vars.dataEntity;

                // if we are dealing with a list of entities

                if (isset(__vars.entities) && (!isset(__vars.inline) || __vars.inline !== true)) {
                    __vars.newRow = jQuery(__vars.view.render('AdminBundle:Admin:cell-collectionRow.html.php', {'entity' : __vars.dataEntity, 'tables' : __vars.tables}));
                    __vars.newRow.find('input[name*="[remove]"]').val(__vars.dataEntity['removed'] ? 'true' : 'false');
                    __vars.newRow.find('input[name*="[id]"]').attr('checked', 'checked');
                    __vars.existing = __vars.search.children('.checkbox').find(implode('', ['input[name^="' , __vars.table , '["][value="' , __vars.dataEntity['id'] , '"]']));
                    if(__vars.existing.length > 0) {
                        __vars.existing.parents('.checkbox').remove();
                    }
                    // insert under the the right heading
                    if(__vars.dataEntity['removed']) {
                        if (__vars.search.find('header.removed').length == 0) {
                            jQuery(__vars.view['slots'].get('cell-collection-header'))
                                .insertBefore(__vars.search.find('.input'))
                                .addClass('removed');
                        }
                        __vars.newRow.insertAfter(__vars.search.find('header.removed'));
                    }
                    else {
                        if (__vars.search.find('header:not(.removed)').length == 0) {
                            jQuery(__vars.view['slots'].get('cell-collection-header')).insertBefore(__vars.search.find('.input'));
                        }
                        __vars.newRow.insertAfter(__vars.search.find('header:not(.removed)'));
                    }
                }
            }
        }

        __vars.headerTitle = '';
        __vars.placeHolder = '';
        for (__vars.for___13 in __vars.tableNames) {
            if(!__vars.tableNames.hasOwnProperty(__vars.for___13)) { continue; }
            __vars.t = __vars.tableNames[__vars.for___13];
            __vars.headerTitle = implode('', [__vars.headerTitle, !empty(__vars.headerTitle) ? '/' : '', (isset(__vars.dataTypes[__vars.t])
                ? count(__vars.dataTypes[__vars.t])
                : 0), ' ', str_replace('ss_', '', __vars.t), 's']);

            __vars.placeHolder = implode('', [!empty(__vars.placeHolder) ? '/' : '', ucfirst(str_replace('ss_', '', __vars.t))]);
        }
        __vars.placeHolder = implode('', ['Search for ', __vars.placeHolder]);
// some final tweak to the input field
        __vars.input.attr('placeholder', __vars.placeHolder);

// if its inline, update header counts
        if (isset(__vars.entities) && (!isset(__vars.inline) || __vars.inline !== true)) {
            __vars.header = __vars.search.find('header:not(.removed)');
            __vars.headerTitle = implode('', ['Members (', __vars.headerTitle, ')']);
            __vars.header.find('label').text(__vars.headerTitle);
            __vars.search.find('header.removed label').text('Removed');
            //if(__vars.search.find('header:not(.removed) ~ .checkbox').length == 0 ||
            //    __vars.search.find('header:not(.removed) + header').length > 0) {
            //    __vars.search.find('header:not(.removed)').remove();
            //}
            if(__vars.search.find('header.removed ~ .checkbox').length == 0 ||
                __vars.search.find('header.removed + header').length > 0) {
                __vars.search.find('header.removed').remove();
            }
        }

// this is the update stuff that we do every time the template is called

        __vars.entityIds = array_values(__vars.entityIds);
//__vars.input.val(isset(__vars.inline) && __vars.inline === true ? implode(' ', __vars.listIds) : '');
// force it to use string keys
        __vars.input.data('tables', __vars.tables)
            .data('oldValue', '')
            .data('entities', __vars.entityIds)
            .attr('data-tables', json_encode(__vars.tables))
            .attr('data-entities', json_encode(__vars.entityIds));
        for (__vars.for___14 in __vars.tableNames) {
            if(!__vars.tableNames.hasOwnProperty(__vars.for___14)) { continue; }
            __vars.t = __vars.tableNames[__vars.for___14];
            __vars.types = isset(__vars.dataTypes[__vars.t]) ? __vars.dataTypes[__vars.t] : [];
            __vars.input.data(__vars.t, __vars.types)
                .attr(implode('', ['data-' , __vars.t]), json_encode(__vars.types));
        }

        /*
         * TODO: merge this with template scripts if we need inline version
         function updateRows (toField, value, item) {
         isSettingSelectize = true;
         var tableName = value.split('-')[0];
         var existing = (toField.data('entities') || []);
         var existingEntities = toField.data(tableName) || [];
         var oldValue = toField.val().split(' ');
         var valueField = toField[0].selectize.settings.valueField;
         var entity;
         if((entity = existingEntities.filter(function (e) {return e[valueField] == item[valueField]})).length > 0) {
         entity[0].remove = item.remove;
         }
         else {
         existingEntities[existingEntities.length] = item;
         }
         if (item.remove) {
         existing = existing.filter(function (i) {return i != item[valueField]});
         oldValue = oldValue.filter(function (i) {return i != item[valueField]});
         }
         else {
         if(existing.indexOf(item[valueField]) == -1) {
         existing[existing.length] = item[valueField];
         }
         if(oldValue.indexOf(item[valueField]) == -1) {
         oldValue[oldValue.length] = item[valueField];
         }
         }
         toField.data('oldValue', oldValue.join(' '));
         toField.data('entities', existing);
         toField.data(tableName, existingEntities);
         isSettingSelectize = false;
         }
         */

        print (__vars.context.html());


    });



//-----------------------------------------------------------footer_packPacks-----------------------------------------------------------

    window.views['footer_packPacks'] = ( function footer_packPacks (__vars) {print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
        + '    <a href="#add-'); print (__vars.table); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '    <div class="invalid-error">' + "\n"
        + '        <span class="pack-error">The pack is missing a title</span>' + "\n"
        + '        <span class="card-error">The list below has errors in it</span>' + "\n"
        + '        <br />' + "\n"
        + '        <a href="#goto-error">Click here to highlight next problem</a>' + "\n"
        + '    </div>' + "\n"
        + '    <a href="#edit-'); print (__vars.table); print('" class="btn">Edit '); print (ucfirst(str_replace('ss_', '', __vars.table))); print('</a>' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('packs')); print('" class="btn cancel-edit">Close</a>' + "\n"
        + '    <a href="#save-'); print (__vars.table); print('" class="more">Save</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------cell_invite_ss_group-----------------------------------------------------------

    window.views['cell_invite_ss_group'] = ( function cell_invite_ss_group (__vars) {print('');



        /** @var Group __vars.ss_group */

        /** @var Invite __vars.invite */
        __vars.invite = __vars.ss_group.getInvites().first();

        print('' + "\n"
            + '<label class="input">' + "\n"
            + '    <input type="hidden" name="invite[id]" value="'); print (!empty(__vars.invite) ? __vars.invite.getId() : ''); print('" />' + "\n"
            + '    <input type="text" name="invite[code]" value="'); print (!empty(__vars.invite) ? __vars.invite.getCode() : ''); print('" />' + "\n"
            + '    <input type="hidden" name="invite[activated]" value="'); print (!empty(__vars.invite) ? __vars.invite.getActivated() : ''); print('" />' + "\n"
            + '    <input type="hidden" name="invite[group]" value="'); print (__vars.ss_group.getId()); print('" />' + "\n"
            + '</label>');});



//-----------------------------------------------------------cell_preview_card-----------------------------------------------------------

    window.views['cell_preview_card'] = ( function cell_preview_card (__vars) {print('');



        /** @var Card __vars.card */

// check if we need to update or create template
        __vars.row = !empty(__vars.context) ? __vars.context : jQuery(__vars.this);
        __vars.preview = __vars.row.find('.preview');
// TODO: how to get data from object or from view in the same way?
// TODO: use applyFields and gatherFields here too?  at the row level?
        if(__vars.preview.length == 0) {
            return;
            __vars.type = __vars.card.getResponseType();
            __vars.content = __vars.card.getContent();
            __vars.content = preg_replace('/\\\\n(\\\\r)?/i', "\n", __vars.content);
            __vars.correct = !empty(__vars.card.getCorrect()) ? preg_replace('/\\\\n(\\\\r)?/i', "\n", __vars.card.getCorrect().getContent()) : '';
            /** @var Answer[] __vars.answers */
            __vars.answersUnique = [];
            for (__vars.for___15 in __vars.card.getAnswers().toArray()) {
                if(!__vars.card.getAnswers().toArray().hasOwnProperty(__vars.for___15)) { continue; }
                __vars.answer = __vars.card.getAnswers().toArray()[__vars.for___15];
                /** @var Answer __vars.answer */
                if(!__vars.answer.getDeleted() && !in_array(__vars.answer.getContent(), __vars.answersUnique)) {
                    __vars.answersUnique[count(__vars.answersUnique)] = __vars.answer.getContent();
                }
            }
            if ((__vars.hasUrl = preg_match('/https:\/\/.*/i', __vars.content, __vars.matches)) > 0) {
                __vars.url = trim(__vars.matches[0]);
            }
        }
        else {
            __vars.type = explode(' ', __vars.row.find('select[name="type"]').val())[0];
            __vars.url = trim(__vars.row.find('input[name="upload"]').val());
            __vars.content = str_replace('\\n', "\n", __vars.row.find('.content textarea').val());
            __vars.answers = explode("\n", __vars.row.find('.correct.type-mc:visible textarea').val());
            __vars.answersUnique = [];
            for (__vars.for___16 in __vars.answers) {
                if(!__vars.answers.hasOwnProperty(__vars.for___16)) { continue; }
                __vars.answer = __vars.answers[__vars.for___16];
                if(!in_array(__vars.answer, __vars.answersUnique)) {
                    __vars.answersUnique[count(__vars.answersUnique)] = __vars.answer;
                }
            }
            __vars.correct = __vars.row.find('.input.correct:visible textarea, .input.correct:visible select, .radio.correct:visible input[type="radio"]:checked, .radios:visible input[type="radio"]:checked').val();
            __vars.correct = str_replace('\\n', "\n", __vars.correct);
        }

        __vars.isImage = false;
        __vars.isAudio = false;
        if(!empty(__vars.url)) {
            __vars.isImage = substr(__vars.url, -4) == '.jpg' || substr(__vars.url, -4) == '.jpeg' || substr(__vars.url, -4) == '.gif' || substr(__vars.url, -4) == '.png';
            __vars.isAudio = substr(__vars.url, -4) == '.mp3' || substr(__vars.url, -4) == '.m4a';
            __vars.content = trim(str_replace(__vars.url, '', __vars.content));
        }

        __vars.template = __vars.type == ''
            ? __vars.preview.find('.preview-card:not([class*="type-"])')
            : __vars.preview.find(implode('', ['.preview-card.type-' , __vars.type]));
// switch templates if needed
        if (2 != __vars.template.length) {
            __vars.preview.children().remove();

            // this is all prompt content
            __vars.view['slots'].start('card-preview-prompt'); print('' + "\n"
                + '    '); if (!empty(__vars.isImage)) { print('<img src="'); print (__vars.url); print('" class="centerized" />'); } print('' + "\n"
                + '    '); if (!empty(__vars.isAudio)) { print('<div class="preview-play"><a href="'); print (__vars.url); print('" class="play centerized"></a><a href="#pause" class="pause centerized"></a></div>'); } print('' + "\n"
                + '    '); if (empty(__vars.isImage) && empty(__vars.isAudio)) { print('' + "\n"
                + '        <div class="preview-content"><div class="centerized">'); print (__vars.view.escape(__vars.content)); print('</div></div>' + "\n"
                + '    '); } print('' + "\n"
                + '    '); __vars.view['slots'].stop();

            // re-render preview completely because type has changed
            __vars.view['slots'].start('card-preview'); print('' + "\n"
                + '    <h3>Preview: </h3>' + "\n"
                + '    '); if (empty(__vars.type)) { print('' + "\n"
                + '        <div class="preview-card">' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-tap">Tap to see answer</div>' + "\n"
                + '        </div>' + "\n"
                + '        <div class="preview-card preview-answer">' + "\n"
                + '            <div class="preview-prompt">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                <div class="preview-correct">Correct answer:</div>' + "\n"
                + '                <div class="preview-content"><div class="centerized"></div></div>' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-wrong">âœ˜</div>' + "\n"
                + '            <div class="preview-guess">Did you guess correctly?</div>' + "\n"
                + '            <div class="preview-right">âœ”ï¸Ž</div>' + "\n"
                + '        </div>' + "\n"
                + '    '); } print('' + "\n"
                + '    '); if (__vars.type == 'mc') { print('' + "\n"
                + '        <div class="preview-card type-mc">' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-response"><div class="centerized"></div></div>' + "\n"
                + '            <div class="preview-response"><div class="centerized"></div></div>' + "\n"
                + '            <div class="preview-response"><div class="centerized"></div></div>' + "\n"
                + '            <div class="preview-response"><div class="centerized"></div></div>' + "\n"
                + '        </div>' + "\n"
                + '    '); } print('' + "\n"
                + '    '); if (__vars.type == 'tf') { print('' + "\n"
                + '        <div class="preview-card type-tf">' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-false">False</div>' + "\n"
                + '            <div class="preview-guess"> </div>' + "\n"
                + '            <div class="preview-true">True</div>' + "\n"
                + '        </div>' + "\n"
                + '    '); } print('' + "\n"
                + '    '); if (__vars.type == 'sa') { print('' + "\n"
                + '        <div class="preview-card type-sa">' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <label class="input"><input type="text" value=""/></label>' + "\n"
                + '        </div>' + "\n"
                + '    '); } print('' + "\n"
                + '    '); if (!empty(__vars.type)) { print('' + "\n"
                + '        <div class="preview-card type-mc type-tf type-sa preview-answer">' + "\n"
                + '            <div class="preview-prompt">' + "\n"
                + '                '); __vars.view['slots'].output('card-preview-prompt'); print('' + "\n"
                + '            </div>' + "\n"
                + '            <div class="preview-inner">' + "\n"
                + '                <div class="preview-correct">Correct answer:</div>' + "\n"
                + '                <div class="preview-content"><div class="centerized"></div></div>' + "\n"
                + '            </div>' + "\n"
                + '        </div>' + "\n"
                + '    '); }
            __vars.view['slots'].stop();

            __vars.preview.append(__vars.view['slots'].get('card-preview'));
        }

//__vars.packTitle = !empty(__vars.card.getPack()) ? __vars.card.getPack().getTitle() : '';
//__vars.cardCount = !empty(__vars.card.getPack()) ? (__vars.card.getIndex() + 1 . ' of ' . __vars.card.getPack().getCards().count()) : '1 or 10';

// replace with image
        if(__vars.isImage && isset(__vars.url)) {
            // TODO: change this if we need to support image and text at the same time not using entry box
            __vars.preview.find('.preview-card:not(.preview-answer) .preview-inner img, .preview-answer .preview-prompt img, .preview-card:not(.preview-answer) .preview-inner .preview-content, .preview-answer .preview-prompt .preview-content')
                .replaceWith(implode('', ['<img src="', __vars.url, '" />']));

            // TODO: if type-sa?
            if(!empty(__vars.content)) {
                __vars.preview.find('[type="text"]').val(__vars.content);
            }
            else {
                __vars.preview.find('[type="text"]').val('Type your answer');
            }
        }
        else {
            __vars.preview.find('[type="text"]').val('Type your answer');
        }

        __vars.preview.find('.preview-content div').text(__vars.content);

        for (__vars.ai = 0; __vars.ai < count(__vars.answersUnique); __vars.ai++) {
            __vars.preview.find('.preview-response').eq(__vars.ai).find('div').text(__vars.answersUnique[__vars.ai]);
        }

        __vars.preview.find('.preview-answer .preview-inner .preview-content div').text(__vars.correct);

        print (__vars.row.html());});



//-----------------------------------------------------------tab-----------------------------------------------------------

    window.views['tab'] = ( function tab (__vars) {print('');












        /** @var User __vars.user */
        __vars.user = __vars.app.getUser();

        __vars.view.extend('StudySauceBundle:Shared:dashboard.html.php');

        __vars.view['slots'].start('stylesheets');
        for (__vars.for___17 in __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'}).hasOwnProperty(__vars.for___17)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})[__vars.for___17]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        for (__vars.for___18 in __vars.view['assetic'].stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], {'output' : 'bundles/admin/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], {'output' : 'bundles/admin/css/*.css'}).hasOwnProperty(__vars.for___18)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], {'output' : 'bundles/admin/css/*.css'})[__vars.for___18]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        __vars.view['slots'].stop();

        __vars.view['slots'].start('javascripts');
        for (__vars.for___19 in __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'}).hasOwnProperty(__vars.for___19)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})[__vars.for___19]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); }
        for (__vars.for___20 in __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], {'output' : 'bundles/admin/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], {'output' : 'bundles/admin/js/*.js'}).hasOwnProperty(__vars.for___20)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], {'output' : 'bundles/admin/js/*.js'})[__vars.for___20]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); } print('' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('body'); print('' + "\n"
            + '    <div class="panel-pane" id="command">' + "\n"
            + '        <div class="pane-content">' + "\n"
            + '            '); print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', {'tables' : ['ss_user']}))); print('' + "\n"
            + '        </div>' + "\n"
            + '    </div>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('sincludes');
        __vars.view['slots'].stop();
    });



//-----------------------------------------------------------cell_roles-----------------------------------------------------------

    window.views['cell_roles'] = ( function cell_roles (__vars) {print('');



        /** @var User|Group __vars.entity */
        print('' + "\n"
            + '' + "\n"
            + '<div>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_PAID" '); print (__vars.entity.hasRole(
            'ROLE_PAID'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>PAID</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_ADMIN" '); print (__vars.entity.hasRole(
            'ROLE_ADMIN'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>ADMIN</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_PARENT" '); print (__vars.entity.hasRole(
            'ROLE_PARENT'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>PARENT</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_PARTNER" '); print (__vars.entity.hasRole(
            'ROLE_PARTNER'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>PARTNER</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_ADVISER" '); print (__vars.entity.hasRole(
            'ROLE_ADVISER'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>ADVISER</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_MASTER_ADVISER" '); print (__vars.entity.hasRole(
            'ROLE_MASTER_ADVISER'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>MASTER_ADVISER</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_DEMO" '); print (__vars.entity.hasRole(
            'ROLE_DEMO'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>DEMO</span></label>' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="roles"' + "\n"
            + '                                   value="ROLE_GUEST" '); print (__vars.entity.hasRole(
            'ROLE_GUEST'
        ) ? 'checked="checked"' : ''); print(' /><i></i><span>GUEST</span></label>' + "\n"
            + '</div>');});



//-----------------------------------------------------------footer_packCards-----------------------------------------------------------

    window.views['footer_packCards'] = ( function footer_packCards (__vars) {print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
        + '    <a href="#add-'); print (__vars.table); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------footer_groupGroups-----------------------------------------------------------

    window.views['footer_groupGroups'] = ( function footer_groupGroups (__vars) {print('');



        print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
            + '    <a href="#edit-'); print (__vars.table); print('" class="btn">Edit '); print (ucfirst(str_replace('ss_', '', __vars.table))); print('</a>' + "\n"
            + '    <a href="'); print (__vars.view['router'].generate('groups')); print('" class="btn cancel-edit">Close</a>' + "\n"
            + '    <a href="#save-'); print (__vars.table); print('" class="more">Save</a>' + "\n"
            + '</div>');});



//-----------------------------------------------------------footer_newPack-----------------------------------------------------------

    window.views['footer_newPack'] = ( function footer_newPack (__vars) {print('<div class="highlighted-link form-actions invalid '); print (__vars.table); print('">' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('packs_new')); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> new '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------cell_status_pack-----------------------------------------------------------

    window.views['cell_status_pack'] = ( function cell_status_pack (__vars) {print('');




        /** @var GlobalVariables __vars.app */

        /** @var Pack __vars.pack */
        __vars.view['slots'].start('cell_status_pack'); print('' + "\n"
            + '    <div>' + "\n"
            + '        <label class="input status">' + "\n"
            + '            <select name="status">' + "\n"
            + '                <option value="UNPUBLISHED">Unpublished</option>' + "\n"
            + '                <option value="GROUP">Published</option>' + "\n"
            + '                ');
        // TODO: if user changes, we need to force the whole template to rebuild
        if (__vars.app.getUser().hasRole('ROLE_ADMIN') && __vars.app.getUser().getEmail() == 'brian@studysauce.com') { print('' + "\n"
            + '                    <option value="PUBLIC">Public</option>' + "\n"
            + '                    <option value="UNLISTED">Unlisted</option>' + "\n"
            + '                    <option value="DELETED">Deleted</option>' + "\n"
            + '                '); } print('' + "\n"
            + '            </select>' + "\n"
            + '        </label>' + "\n"
            + '    </div>' + "\n"
            + '');
        __vars.view['slots'].stop();


// update the template
        __vars.row = jQuery(__vars.this);

// TODO: generalize this in a cell-select generic template
        __vars.select = __vars.row.find('select');
        if (__vars.select.length == 0) {
            __vars.select = __vars.row.append(__vars.view['slots'].get('cell_status_pack')).find('select');
            // TODO: this could be some sort of binding API
            __vars.value = __vars.pack.getStatus();
            __vars.publish = {
                'schedule' : !empty(__vars.pack.getProperty('schedule'))
                    ? __vars.pack.getProperty('schedule').format('r')
                    : '',
                'email' : __vars.pack.getProperty('email'),
                'alert' : __vars.pack.getProperty('alert'),
            };
            // create update code vs read code below?
            __vars.select.val(empty(__vars.value) ? '' : __vars.value);
            __vars.select.attr('data-publish', json_encode(__vars.publish));
            __vars.select.find(implode('', ['option[value="', __vars.value, '"]'])).attr('selected', 'selected');
        } else {
// TODO: this is update code specific to status field, generalize this in model
            __vars.publish = __vars.select.data('publish');
            __vars.value = __vars.select.val();
        }
        __vars.schedule = new Date(__vars.publish['schedule']);

// TODO: this is specific to status
        __vars.select.parents('.status').attr('class', implode('', ['status ' , strtolower(__vars.value) , (__vars.schedule <= new Date() ? '' : ' pending')]));

// set schedule data
        __vars.select.find('option[value="GROUP"]').text(__vars.schedule > new Date()
            ? implode('', ['Pending (', __vars.schedule.format('m/d/Y H:m'), ')'])
            : (!empty(__vars.schedule) ? 'Published' : 'Publish'));


        print (__vars.row.html());

    });



//-----------------------------------------------------------cell_id_pack-----------------------------------------------------------

    window.views['cell_id_pack'] = ( function cell_id_pack (__vars) {print('');




        /** @var Pack __vars.pack */
        __vars.time = method_exists(__vars.pack, 'getModified') && !empty(__vars.pack.getModified()) ? __vars.pack.getModified() : __vars.pack.getCreated();

        if (empty(__vars.pack.getLogo())) { print('' + "\n"
            + '        <img width="300" height="100" src="'); print (__vars.view.escape(__vars.view['assets'].getUrl('bundles/studysauce/images/upload_image.png'))); print('" class="default centerized" alt="Upload"/>' + "\n"
            + '    ');
        } else { print('<img height="50" src="'); print (__vars.pack.getLogo()); print('" class="centerized" />'); } print('');
    });



//-----------------------------------------------------------row_card-----------------------------------------------------------

    window.views['row_card'] = ( function row_card (__vars) {print('');






        /** @var GlobalVariables __vars.app */

        /** @var Card __vars.card */

        __vars.rowId = implode('', [__vars.table , '-id-' , __vars.card.getId()]);

        __vars.expandable = isset(__vars.request['expandable']) && is_array(__vars.request['expandable'])
            ? __vars.request['expandable']
            : [];
        print('' + "\n"
            + '<div class="'); print (__vars.table); print('-row '); print (empty(__vars.card.getResponseType()) || __vars.card.getResponseType() == 'fc' ? '' : implode('', ['type-' , strtolower(__vars.card.getResponseType())])); print(' ');
        print (__vars.rowId); print(' ');
        print (isset(__vars.request['edit']) && (__vars.request['edit'] === true || is_array(__vars.request['edit']) && in_array(__vars.table, __vars.request['edit']))
            ? 'edit'
            : (isset(__vars.request['read-only']) && (__vars.request['read-only'] === false || is_array(__vars.request['read-only']) && !in_array(__vars.table, __vars.request['read-only']))
            ? ''
            : 'read-only')); print(' ');
        print (isset(__vars.expandable[__vars.table]) ? 'expandable' : ''); print(' ');
        print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cells.html.php', {'entity' : __vars.card, 'tables' : __vars.tables, 'table' : __vars.table, 'allGroups' : __vars.allGroups, 'request' : __vars.request, 'results' : __vars.results})); print('' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>' + "\n"
            + '</div>' + "\n"
            + ''); if (isset(__vars.expandable[__vars.table])) { print('' + "\n"
            + '    <div class="expandable ');
            print (!empty(__vars.classes) ? __vars.classes : ''); print('">' + "\n"
                + '    '); print (__vars.view.render('AdminBundle:Admin:cells.html.php', {'entity' : __vars.card, 'tables' : __vars.expandable, 'table' : __vars.table, 'allGroups' : __vars.allGroups, 'request' : __vars.request, 'results' : __vars.results})); print('' + "\n"
                + '    </div>');
        }});



//-----------------------------------------------------------cell_correct_card-----------------------------------------------------------

    window.views['cell_correct_card'] = ( function cell_correct_card (__vars) {print('');




        AdminController.__vars.radioCounter++;
        /** @var Card __vars.card */

        __vars.answers = [];
        for (__vars.for___21 in __vars.card.getAnswers().toArray()) {
            if(!__vars.card.getAnswers().toArray().hasOwnProperty(__vars.for___21)) { continue; }
            __vars.a = __vars.card.getAnswers().toArray()[__vars.for___21];
            /** @var Answer __vars.a */
            if(!__vars.a.getDeleted() && !in_array(__vars.a.getValue(), __vars.answers)) {
                __vars.answers[count(__vars.answers)] = __vars.a.getValue();
            }
        }
        if(empty(__vars.answers)) {
            __vars.answers = [''];
        }

        print('' + "\n"
            + '<label class="input correct">' + "\n"
            + '    <textarea name="correct" placeholder="Answer">'); print (__vars.view.escape(!empty(__vars.card.getCorrect()) ? __vars.view.escape(__vars.card.getCorrect().getValue()) : trim(__vars.card.getResponseContent()))); print('</textarea>' + "\n"
            + '</label>' + "\n"
            + '<div class="correct type-mc">' + "\n"
            + '    <div class="radios">' + "\n"
            + '        '); for (__vars.for___22 in __vars.answers) {
            if(!__vars.answers.hasOwnProperty(__vars.for___22)) { continue; }
            __vars.a = __vars.answers[__vars.for___22]; print('' + "\n"
                + '            <label class="radio"><input type="radio" name="correct-mc-'); print (!empty(__vars.card.getId()) ? __vars.card.getId() : AdminController.__vars.radioCounter); print('" value="'); print (__vars.view.escape(__vars.a)); print('" '); print (!empty(__vars.card.getCorrect()) && __vars.a == __vars.card.getCorrect().getValue() ? 'checked="checked"' : ''); print(' /><i></i><span>'); print (__vars.view.escape(__vars.a)); print('</span></label>' + "\n"
                + '        '); } print('' + "\n"
            + '    </div>' + "\n"
            + '    <label class="input">' + "\n"
            + '        <textarea name="answers" placeholder="Answers">'); print (implode("\n", __vars.answers)); print('</textarea>' + "\n"
            + '    </label>' + "\n"
            + '</div>' + "\n"
            + '<label class="radio correct type-tf">' + "\n"
            + '    <input type="radio" name="correct-'); print (!empty(__vars.card.getId()) ? __vars.card.getId() : AdminController.__vars.radioCounter); print('" value="true" '); print (!empty(__vars.card.getCorrect()) && preg_match('/t/i', __vars.card.getCorrect().getValue()) ? 'checked="checked"' : ''); print(' />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>True</span>' + "\n"
            + '</label>' + "\n"
            + '<label class="radio correct type-tf">' + "\n"
            + '    <input type="radio" name="correct-'); print (!empty(__vars.card.getId()) ? __vars.card.getId() : AdminController.__vars.radioCounter); print('" value="false" '); print (!empty(__vars.card.getCorrect()) && preg_match('/f/i', __vars.card.getCorrect().getValue()) ? 'checked="checked"' : ''); print(' />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>False</span>' + "\n"
            + '</label>' + "\n"
            + '<label class="input correct type-sa">' + "\n"
            + '    <textarea name="correct" placeholder="Answer">'); print (__vars.view.escape(!empty(__vars.card.getCorrect()) ? trim(__vars.card.getCorrect().getValue(), '__vars.^') : '')); print('</textarea>' + "\n"
            + '</label>');
    });



//-----------------------------------------------------------header_createSubGroups-----------------------------------------------------------

    window.views['header_createSubGroups'] = ( function header_createSubGroups (__vars) {print('');



        __vars.entityIds = [];
        for (__vars.for___23 in __vars.results['ss_group']) {
            if(!__vars.results['ss_group'].hasOwnProperty(__vars.for___23)) { continue; }
            __vars.p = __vars.results['ss_group'][__vars.for___23];
            /** @var Group __vars.p */
            __vars.entityIds[count(__vars.entityIds)] = implode('', ['ss_group-' , __vars.p.getId()]);
        }

        print('' + "\n"
            + '<header class="'); print (__vars.table); print('">' + "\n"
            + '    <label>Subgroups</label>' + "\n"
            + '    <label>Members</label>' + "\n"
            + '    <label>Packs</label>' + "\n"
            + '    <a href="#create-entity" data-target="#create-entity" data-toggle="modal"' + "\n"
            + '       name="pack[groups]"' + "\n"
            + '       data-tables="'); print (__vars.view.escape(json_encode({'ss_group' : AdminController.__vars.defaultMiniTables['ss_group']}))); print('"' + "\n"
            + '       data-entities="'); print (__vars.view.escape(json_encode(__vars.entityIds))); print('"' + "\n"
            + '       data-action="'); print (__vars.view['router'].generate('save_group', {
            'pack' : {'id' : __vars.request['pack-id']},
            'tables' : {'pack' : ['groups']}
        })); print('" class="big-add">Add' + "\n"
            + '        <span>+</span> new subgroup</a>' + "\n"
            + '</header>');
    });



//-----------------------------------------------------------cell_idEdit_pack-----------------------------------------------------------

    window.views['cell_idEdit_pack'] = ( function cell_idEdit_pack (__vars) {print('');




        /** @var Pack __vars.pack */
        print('' + "\n"
            + '<a href="#upload-image" data-target="#upload-file" data-toggle="modal" class="pack-icon cloak centerized">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cell-id-pack.html.php', {'pack' : __vars.pack})); print('' + "\n"
            + '    <input name="logo" value="'); print (!empty(__vars.pack.getLogo()) ? __vars.pack.getLogo() : ''); print('" type="hidden" />' + "\n"
            + '    <span class="reveal"> Image</span>' + "\n"
            + '</a>');
    });



//-----------------------------------------------------------cell_idEdit_ss_group-----------------------------------------------------------

    window.views['cell_idEdit_ss_group'] = ( function cell_idEdit_ss_group (__vars) {print('');




        /** @var Group __vars.ss_group */
        __vars.time = method_exists(__vars.ss_group, 'getModified') && !empty(__vars.ss_group.getModified()) ? __vars.ss_group.getModified() : __vars.ss_group.getCreated();
        print('' + "\n"
            + '<a href="#upload-image" data-target="#upload-file" data-toggle="modal" class="pack-icon cloak centerized">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cell-id-ss_group.html.php', {'ss_group' : __vars.ss_group})); print('' + "\n"
            + '    <input name="logo" value="'); print (!empty(__vars.ss_group.getLogo()) ? __vars.ss_group.getLogo().getUrl() : ''); print('" type="hidden" />' + "\n"
            + '    <span class="reveal"> Image</span>' + "\n"
            + '</a>');});



//-----------------------------------------------------------cell_name_card-----------------------------------------------------------

    window.views['cell_name_card'] = ( function cell_name_card (__vars) {print('');


        /** @var Card __vars.card */

        __vars.content = __vars.card.getContent();
        __vars.content = preg_replace('/\\\\n(\\\\r)?/i', "\n", __vars.content);
        if ((__vars.hasUrl = preg_match('/https:\/\/.*/i', __vars.content, __vars.matches)) > 0) {
            __vars.url = trim(__vars.matches[0]);
            __vars.isImage = substr(__vars.url, -4) == '.jpg' || substr(__vars.url, -4) == '.jpeg' || substr(__vars.url, -4) == '.gif' || substr(__vars.url, -4) == '.png';
            __vars.isAudio = substr(__vars.url, -4) == '.mp3' || substr(__vars.url, -4) == '.m4a';
            __vars.content = preg_replace('/\s*\n\r?/i', '\n', trim(str_replace(__vars.url, '', __vars.content)));
        }
        print('' + "\n"
            + '' + "\n"
            + '<label class="input content">' + "\n"
            + '    <textarea name="content" placeholder="Prompt">'); print (__vars.view.escape(__vars.content)); print('</textarea>' + "\n"
            + '</label>');
    });



//-----------------------------------------------------------cell_title_pack-----------------------------------------------------------

    window.views['cell_title_pack'] = ( function cell_title_pack (__vars) {print('');





        /** @var Pack __vars.pack */

        if (isset(__vars.request['pack-id']) && __vars.pack.getId() == __vars.request['pack-id']) {
            print (__vars.view.render('AdminBundle:Admin:cell-label.html.php', {'fields' : ['All users (not in subgroups below)', 0, 0]}));
        } else { print('' + "\n"
            + '    <a href="'); print (__vars.view['router'].generate('packs_edit', {'pack' : __vars.pack.getId()})); print('">' + "\n"
            + '    ');
            __vars.userCount = 0;
            for (__vars.for___24 in __vars.pack.getUsers().toArray()) {
                if(!__vars.pack.getUsers().toArray().hasOwnProperty(__vars.for___24)) { continue; }
                __vars.u = __vars.pack.getUsers().toArray()[__vars.for___24];
                /** @var User __vars.u */
                if (!empty(__vars.up = __vars.u.getUserPack(__vars.pack))) {
                    __vars.userCount += !empty(__vars.up.getDownloaded()) ? 1 : 0;
                }
            }
            if (isset(__vars.request['ss_group-id']) && !empty(__vars.group = __vars.request['ss_group-id'])) {
                __vars.userGroupCount = 0;
                for (__vars.for___25 in __vars.pack.getUsers().toArray()) {
                    if(!__vars.pack.getUsers().toArray().hasOwnProperty(__vars.for___25)) { continue; }
                    __vars.u = __vars.pack.getUsers().toArray()[__vars.for___25];
                    if(!empty(__vars.up = __vars.u.getUserPack(__vars.pack)) && !empty(__vars.up.getDownloaded())) {
                        /** @var User __vars.u */
                        for (__vars.for___26 in __vars.u.getGroups().toArray()) {
                            if(!__vars.u.getGroups().toArray().hasOwnProperty(__vars.for___26)) { continue; }
                            __vars.g = __vars.u.getGroups().toArray()[__vars.for___26];
                            /** @var Group __vars.g */
                            if(__vars.g.getId() == __vars.request['ss_group-id']) {
                                __vars.userGroupCount += 1;
                                break;
                            }
                        }
                    }
                }
                __vars.userCount = __vars.userGroupCount;
            }

            __vars.cardCount = 0;
            for (__vars.for___27 in __vars.pack.getCards().toArray()) {
                if(!__vars.pack.getCards().toArray().hasOwnProperty(__vars.for___27)) { continue; }
                __vars.c = __vars.pack.getCards().toArray()[__vars.for___27];
                /** @var Card __vars.c */
                __vars.cardCount += !__vars.c.getDeleted() ? 1 : 0;
            }

            print (__vars.view.render('AdminBundle:Admin:cell-label.html.php', {'fields' : [__vars.pack.getTitle(), __vars.userCount, __vars.cardCount]}));
            print('' + "\n"
                + '    </a>' + "\n"
                + ''); }});



//-----------------------------------------------------------cell_titleNew_pack-----------------------------------------------------------

    window.views['cell_titleNew_pack'] = ( function cell_titleNew_pack (__vars) {print('');




        /** @var Pack __vars.pack */
        /** @var User __vars.user */
        if(!empty(__vars.pack.getUser()) && __vars.pack.getUser().getId() == __vars.request['ss_user-id']) {
            __vars.user = __vars.pack.getUser();
        }
        else {
            __vars.user = __vars.pack.getUserById(__vars.request['ss_user-id']);
        }
        __vars.isNew = true;
        if(!empty(__vars.user)) {
            __vars.isNew = __vars.pack.isNewForChild(__vars.user);
        }
        print('' + "\n"
            + '' + "\n"
            + '<label>'); print (__vars.isNew ? '<strong>New </strong>' : ''); print('<span>'); print (__vars.view.escape(__vars.pack.getTitle())); print('</span></label>');});



//-----------------------------------------------------------footer_subGroups-----------------------------------------------------------

    window.views['footer_subGroups'] = ( function footer_subGroups (__vars) {print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
        + '    '); if (empty(__vars.results['ss_group'])) { print(' <div class="empty-packs">No subgroups</div> '); } print('' + "\n"
        + '</div>' + "\n"
        + '');
    });



//-----------------------------------------------------------cell_generic-----------------------------------------------------------

    window.views['cell_generic'] = ( function cell_generic (__vars) {print('');






        __vars.searchTables = [];

        /** @var Group|Pack __vars.entity */
        for (__vars.for___28 in __vars.fields) {
            if(!__vars.fields.hasOwnProperty(__vars.for___28)) { continue; }
            __vars.subfield = __vars.fields[__vars.for___28];
            __vars.joinTable = __vars.table;
            __vars.joinName = __vars.table;
            __vars.joinFields = explode('.', __vars.subfield);
            for (__vars.for___29 in __vars.joinFields) {
                if(!__vars.joinFields.hasOwnProperty(__vars.for___29)) { continue; }
                __vars.jf = __vars.joinFields[__vars.for___29];
                __vars.associated = AdminController.__vars.allTables[__vars.joinTable].getAssociationMappings();
                if (isset(__vars.associated[__vars.jf])) {
                    __vars.te = __vars.associated[__vars.jf]['targetEntity'];
                    __vars.ti = array_search(__vars.te, AdminController.__vars.allTableClasses);
                    if (__vars.ti !== false) {
                        __vars.joinTable = AdminController.__vars.allTableMetadata[__vars.ti].table['name'];
                    } else {
                        continue;
                    }
                    __vars.newName = implode('', [__vars.joinName , '_' , preg_replace('[^a-z]', '_', __vars.jf) , __vars.joinTable]);
                    __vars.joinName = __vars.newName;
                } else {
                    // join failed, don't search any other tables this round
                    __vars.joinName = null;
                    break;
                }
            }
            // do one search on the last entity on the join, ie not searching intermediate tables like user_pack or ss_user_group
            if (!empty(__vars.joinName) && isset(AdminController.__vars.defaultTables[__vars.joinTable])) {
                __vars.searchTables[__vars.joinTable] = AdminController.__vars.defaultTables[__vars.joinTable]['name'];
            }
        }

        if (count(__vars.searchTables) > 0 && method_exists(__vars.entity, implode('', ['get' , ucfirst(__vars.field)]))) {
            __vars.result = call_user_func_array([__vars.entity, implode('', ['get' , ucfirst(__vars.field)])], []);
            print (__vars.view.render('AdminBundle:Admin:cell-collection.html.php', {'tables' : __vars.searchTables, 'entities' : __vars.result.toArray(), 'inline' : true}));
        }

    });



//-----------------------------------------------------------cell_packMastery_pack-----------------------------------------------------------

    window.views['cell_packMastery_pack'] = ( function cell_packMastery_pack (__vars) {print('');

        /** @var Pack __vars.pack */




        __vars.cardCount = 0;
        for (__vars.for___30 in __vars.pack.getCards().toArray()) {
            if(!__vars.pack.getCards().toArray().hasOwnProperty(__vars.for___30)) { continue; }
            __vars.c = __vars.pack.getCards().toArray()[__vars.for___30];
            /** @var Card __vars.c */
            if(!__vars.c.getDeleted()) {
                __vars.cardCount += 1;
            }
        }

        if(!empty(__vars.pack.getUser()) && __vars.pack.getUser().getId() == __vars.request['ss_user-id']) {
            __vars.user = __vars.pack.getUser();
        }
        else {
            __vars.user = __vars.pack.getUserById(__vars.request['ss_user-id']);
        }
        __vars.retentionCount = 0;
        if(!empty(__vars.user)) {
            __vars.retention = PacksController.getRetention(__vars.pack, __vars.user);
            for (__vars.for___31 in __vars.retention) {
                if(!__vars.retention.hasOwnProperty(__vars.for___31)) { continue; }
                __vars.r = __vars.retention[__vars.for___31];
                if(__vars.r[2]) {
                    __vars.retentionCount += 1;
                }
            }
        }
        if(__vars.cardCount > 0) {
            __vars.mastery = round((__vars.cardCount - __vars.retentionCount) / __vars.cardCount * 100.0);
        }
        else {
            __vars.mastery = 0;
        }

        print('' + "\n"
            + '<label style="padding-left:'); print (__vars.mastery); print('%;width:'); print (__vars.mastery); print('%;">&nbsp;'); print (__vars.mastery); print('</label>');
    });



//-----------------------------------------------------------heading_packs-----------------------------------------------------------

    window.views['heading_packs'] = ( function heading_packs (__vars) {print('<label class="input">' + "\n"
        + '    <input type="text" name="packs" value="" placeholder="Any Pack / Id"/>' + "\n"
        + '</label>');});



//-----------------------------------------------------------header_subGroups-----------------------------------------------------------

    window.views['header_subGroups'] = ( function header_subGroups (__vars) {print('<header class="'); print (__vars.table); print('">' + "\n"
        + '    <label>Subgroups</label>' + "\n"
        + '    <label>Members</label>' + "\n"
        + '    <label>Packs</label>' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('groups_new')); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> new subgroup</a>' + "\n"
        + '</header>');
    });



//-----------------------------------------------------------results-----------------------------------------------------------

    window.views['results'] = ( function results (__vars) {print('');





        /** @var GlobalVariables __vars.app */

        __vars.context = !empty(__vars.context) ? __vars.context : jQuery(__vars.this);
        __vars.resultOutput = __vars.context.filter('.results');

        __vars.selected = __vars.resultOutput.find('[class*="-row"].selected');

        __vars.resultOutput.find('.view, .template, .template + .expandable:not([class*="-row"]), header, footer, .highlighted-link, [class*="-row"]:not(.edit), [class*="-row"]:not(.edit) + .expandable:not([class*="-row"])').remove();

        __vars.subVars = {
            'allGroups' : __vars.allGroups,
            'request' : __vars.request,
            'results' : __vars.results
        };

        if(__vars.resultOutput.length == 0) {
            __vars.resultOutput = __vars.context.append('<div class="results"></div>').find('.results');
        }
        __vars.resultOutput.data('request', __vars.request).attr('data-request', json_encode(__vars.request))
            .addClass(isset(__vars.request['classes']) && is_array(__vars.request['classes'])
                ? implode(' ', __vars.request['classes'])
                : '');


// TODO: bring back search header for list format
//if (!isset(__vars.request['headers'])) {
//    print (__vars.view.render('AdminBundle:Admin:header-search.html.php', array_merge(__vars.subVars, {'tables' : __vars.tables})));
//}

        if(__vars.app.getUser().getEmailCanonical() == 'brian@studysauce.com') {
            __vars.view['slots'].start('view-settings');
            if(!empty(__vars.request['views'])) { print('<div class="views"><ul>');
                for (__vars.v in __vars.request['views']) {
                    if(!__vars.request['views'].hasOwnProperty(__vars.v)) { continue; }
                    __vars.extend = __vars.request['views'][__vars.v];
                    print('<li><a href="#switch-view-'); print (__vars.v); print('">'); print (__vars.v); print('</a></li>');
                } print('</ul></div>');
            }
            else { print('' + "\n"
                + '        <div class="views"><ul><li><a href="#switch-view-" data-extend="{}">Refresh</a></li></div>' + "\n"
                + '    '); }
            __vars.view['slots'].stop();

            __vars.resultOutput.prepend(__vars.last = jQuery(__vars.view['slots'].get('view-settings')).find('.views'));
        }

        for (__vars.table in __vars.tables) {
            if(!__vars.tables.hasOwnProperty(__vars.table)) { continue; }
            __vars.t = __vars.tables[__vars.table];
            __vars.tableParts = explode('-', __vars.table);
            __vars.ext = implode('-', array_splice(__vars.tableParts, 1));
            __vars.table = explode('-', __vars.table)[0];
            __vars.aliasedRequest = {};
            if(strlen(__vars.ext) > 0) {
                __vars.ext = implode('', ['-' , __vars.ext]);
                __vars.aliasLen = strlen(__vars.table) + strlen(__vars.ext);
                for (__vars.r in __vars.request) {
                    if(!__vars.request.hasOwnProperty(__vars.r)) { continue; }
                    __vars.s = __vars.request[__vars.r];
                    if (substr(__vars.r, 0, __vars.aliasLen) == implode('', [__vars.table , __vars.ext])) {
                        __vars.aliasedRequest[substr(__vars.r, __vars.aliasLen)] = __vars.s;
                    }
                }
                __vars.aliasedRequest['tables'][__vars.table] = __vars.request['tables'][implode('', [__vars.table , __vars.ext])];
            }
            __vars.aliasedRequest = array_merge(__vars.request, __vars.aliasedRequest);
            __vars.subVars = array_merge(__vars.subVars, {'request' : __vars.aliasedRequest, 'tables' : __vars.aliasedRequest['tables']});

            __vars.isNew = isset(__vars.aliasedRequest['new']) && (__vars.aliasedRequest['new'] === true
                || is_array(__vars.aliasedRequest['new']) && in_array(__vars.table, __vars.aliasedRequest['new']));

            // show header template
            if (count(__vars.results[implode('', [__vars.table , __vars.ext])]) > 0 || __vars.isNew) {
                __vars.header = null;
                if (!isset(__vars.aliasedRequest['headers']) || is_array(__vars.headers = __vars.aliasedRequest['headers'])
                    && isset(__vars.headers[__vars.table]) && __vars.headers[__vars.table] === true) {
                    __vars.header = jQuery(__vars.view.render('AdminBundle:Admin:header.html.php',                                         array_merge(__vars.subVars, {'table' : __vars.table})));
                } else if (is_array(__vars.headers = __vars.aliasedRequest['headers'])
                    && isset(__vars.headers[__vars.table])
                    && __vars.view.exists(implode('', ['AdminBundle:Admin:header-' , __vars.headers[__vars.table] , '.html.php']))) {
                    __vars.header = jQuery(__vars.view.render(implode('', ['AdminBundle:Admin:header-' , __vars.headers[__vars.table] , '.html.php']), array_merge(__vars.subVars, {'table' : __vars.table})));
                }

                if(empty(__vars.last) || __vars.last.length == 0) {
                    __vars.resultOutput.prepend(__vars.header);
                }
                else {
                    __vars.last.after(__vars.header);
                }

                if(!empty(__vars.header) && __vars.header.length > 0) {
                    __vars.last = __vars.header.last();
                }
            }

            if(__vars.resultOutput.find(implode('', ['.results-', __vars.table , __vars.ext])).length > 0) {
                __vars.last = __vars.resultOutput.find(implode('', ['.results-', __vars.table , __vars.ext])).last();
            }

            // print out all result entities
            __vars.classes = '';
            for (__vars.for___35 in __vars.results[implode('', [__vars.table , __vars.ext])]) {
                if(!__vars.results[implode('', [__vars.table , __vars.ext])].hasOwnProperty(__vars.for___35)) { continue; }
                __vars.entity = __vars.results[implode('', [__vars.table , __vars.ext])][__vars.for___35];
                __vars.row = null;
                if (__vars.view.exists(implode('', ['AdminBundle:Admin:row-' , __vars.table , '.html.php']))) {
                    __vars.rowVars = array_merge(__vars.subVars, {'classes' : __vars.classes, 'table' : __vars.table});
                    __vars.rowVars[__vars.table] = __vars.entity;
                    __vars.row = jQuery(__vars.view.render(implode('', ['AdminBundle:Admin:row-' , __vars.table , '.html.php']),                 __vars.rowVars));
                } else {
                    __vars.row = jQuery(__vars.view.render('AdminBundle:Admin:row.html.php',                                               array_merge(__vars.subVars, {'classes' : __vars.classes, 'entity' : __vars.entity, 'table' : __vars.table})));
                }
                // TODO: update new row IDs, no insert if(isset(__vars.entity.newId))
                if(empty(__vars.last) || __vars.last.length == 0) {
                    __vars.resultOutput.prepend(__vars.row);
                }
                else {
                    __vars.last.after(__vars.row);
                }

                if(!empty(__vars.row) && __vars.row.length > 0) {
                    __vars.last = __vars.row.last();
                }
            }

            // print out new rows with blank objects
            if (__vars.isNew) {
                __vars.entity = AdminController.createEntity(__vars.table);
                __vars.classes = ' empty';
                __vars.newCount = !empty(intval(__vars.aliasedRequest[implode('', ['count-' , __vars.table])]))
                    ? intval(__vars.aliasedRequest[implode('', ['count-' , __vars.table])])
                    : 1;
                for (__vars.nc = 0; __vars.nc < __vars.newCount; __vars.nc++) {
                    __vars.newRow = null;
                    if (__vars.view.exists(implode('', ['AdminBundle:Admin:row-' , __vars.table , '.html.php']))) {
                        __vars.rowVars = array_merge(__vars.subVars, {'classes' : __vars.classes, 'table' : __vars.table});
                        __vars.rowVars[__vars.table] = __vars.entity;
                        __vars.newRow = jQuery(__vars.view.render(implode('', ['AdminBundle:Admin:row-' , __vars.table , '.html.php']),          __vars.rowVars));
                    } else {
                        __vars.newRow = jQuery(__vars.view.render('AdminBundle:Admin:row.html.php',                                        array_merge(__vars.subVars, {'classes' : __vars.classes, 'entity' : __vars.entity, 'table' : __vars.table})));
                    }
                    if(empty(__vars.last) || __vars.last.length == 0) {
                        __vars.resultOutput.prepend(__vars.newRow);
                    }
                    else {
                        __vars.last.after(__vars.newRow);
                    }

                    if(!empty(__vars.newRow) && __vars.newRow.length > 0) {
                        __vars.last = __vars.newRow.last();
                    }
                }
            }

            // show footer at the end of each result list
            __vars.footer = null;
            if (!isset(__vars.aliasedRequest['footers']) || is_array(__vars.footers = __vars.aliasedRequest['footers'])
                && isset(__vars.footers[__vars.table]) && __vars.footers[__vars.table] === true) {
                __vars.footer = jQuery(__vars.view.render('AdminBundle:Admin:footer.html.php',                                             array_merge(__vars.subVars, {'table' : __vars.table})));
            } else if (is_array(__vars.footers = __vars.aliasedRequest['footers'])
                && isset(__vars.footers[__vars.table])
                && __vars.view.exists(implode('', ['AdminBundle:Admin:footer-' , __vars.footers[__vars.table] , '.html.php']))) {
                __vars.footer = jQuery(__vars.view.render(implode('', ['AdminBundle:Admin:footer-' , __vars.footers[__vars.table] , '.html.php']),     array_merge(__vars.subVars, {'table' : __vars.table})));
            }

            if(empty(__vars.last) || __vars.last.length == 0) {
                __vars.resultOutput.prepend(__vars.footer);
            }
            else {
                __vars.last.after(__vars.footer);
            }

            if(!empty(__vars.footer) && __vars.footer.length > 0) {
                __vars.last = __vars.footer.last();
            }
        }

        print (__vars.context.html());
    });



//-----------------------------------------------------------header_newGroup-----------------------------------------------------------

    window.views['header_newGroup'] = ( function header_newGroup (__vars) {print('<header class="'); print (__vars.table); print('">' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('groups_new')); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> new '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '</header>');});



//-----------------------------------------------------------pack_publish-----------------------------------------------------------

    window.views['pack_publish'] = ( function pack_publish (__vars) {print(''); __vars.view.extend('AdminBundle:Admin:dialog.html.php');

        __vars.view['slots'].start('modal-body'); print('' + "\n"
            + '<label class="radio">' + "\n"
            + '    <input type="radio" name="date" value="now" />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>Publish now</span>' + "\n"
            + '</label><br/>' + "\n"
            + '<label class="radio">' + "\n"
            + '    <input type="radio" name="date" value="later" checked="checked" />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>Publish later</span>' + "\n"
            + '</label><br />' + "\n"
            + '<label class="input">' + "\n"
            + '    <input type="text" name="schedule" placeholder="Date/time" />' + "\n"
            + '</label>' + "\n"
            + '<h3>Notifications:</h3>' + "\n"
            + '<label class="checkbox">' + "\n"
            + '    <input type="checkbox" name="email" value="true" checked="checked" />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>Email sent to user when pack publishes</span>' + "\n"
            + '</label><br/>' + "\n"
            + '<label class="checkbox">' + "\n"
            + '    <input type="checkbox" name="alert" value="true" checked="checked" />' + "\n"
            + '    <i></i>' + "\n"
            + '    <span>In-app alert sent when pack publishes</span>' + "\n"
            + '</label>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('modal-footer'); print('' + "\n"
            + '<a href="#submit-publish" class="btn btn-primary" data-dismiss="modal">Publish</a>' + "\n"
            + ''); __vars.view['slots'].stop(); print('');
    });



//-----------------------------------------------------------footer_groupPacks-----------------------------------------------------------

    window.views['footer_groupPacks'] = ( function footer_groupPacks (__vars) {print('');




        print('' + "\n"
            + '<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
            + '    '); if(empty(__vars.results['pack'])) { print('' + "\n"
            + '        <div class="empty-packs">No packs in this group or all subgroups</div>' + "\n"
            + '        ');
        } print('' + "\n"
            + '</div>' + "\n"
            + '');
    });



//-----------------------------------------------------------heading_status-----------------------------------------------------------

    window.views['heading_status'] = ( function heading_status (__vars) {print('<label class="input">' + "\n"
        + '    <select name="status">' + "\n"
        + '        <option value="">Status</option>' + "\n"
        + '        <option value="UNPUBLISHED">Unpublished</option>' + "\n"
        + '        <option value="PUBLIC">Public</option>' + "\n"
        + '        <option value="GROUP">Group-only</option>' + "\n"
        + '        <option value="UNLISTED">Unlisted</option>' + "\n"
        + '        <option value="DELETED">Deleted</option>' + "\n"
        + '    </select>' + "\n"
        + '</label>');});



//-----------------------------------------------------------cell_packList_pack-----------------------------------------------------------

    window.views['cell_packList_pack'] = ( function cell_packList_pack (__vars) {print('');





        /** @var User|Group __vars.ss_group */
        /** @var Pack __vars.pack */

        __vars.groups = [];
        __vars.groupIds = [];
        __vars.groupUsers = 0;
        for (__vars.for___36 in __vars.pack.getGroups().toArray()) {
            if(!__vars.pack.getGroups().toArray().hasOwnProperty(__vars.for___36)) { continue; }
            __vars.g = __vars.pack.getGroups().toArray()[__vars.for___36];
            /** @var Group __vars.g */
            if(!__vars.g.getDeleted()) {
                __vars.groups[count(__vars.groups)] = __vars.g;
                __vars.groupIds[count(__vars.groupIds)] = __vars.g.getId();
                __vars.groupUsers += __vars.g.getUsers().count();
            }
        }
        /** @var User[] __vars.users */
        __vars.users = __vars.pack.getUsers().toArray();
// only show the users not included in any groups
        __vars.diffUsers = [];
        for (__vars.for___37 in __vars.users) {
            if(!__vars.users.hasOwnProperty(__vars.for___37)) { continue; }
            __vars.u = __vars.users[__vars.for___37];
            __vars.shouldExclude = false;
            for (__vars.for___38 in __vars.u.getGroups().toArray()) {
                if(!__vars.u.getGroups().toArray().hasOwnProperty(__vars.for___38)) { continue; }
                __vars.g = __vars.u.getGroups().toArray()[__vars.for___38];
                if(in_array(__vars.g.getId(), __vars.groupIds)) {
                    __vars.shouldExclude = true;
                }
            }
            if(!__vars.shouldExclude) {
                __vars.diffUsers[count(__vars.diffUsers)] = __vars.u;
            }
        }

        __vars.cardCount = 0;
        for (__vars.for___39 in __vars.pack.getCards().toArray()) {
            if(!__vars.pack.getCards().toArray().hasOwnProperty(__vars.for___39)) { continue; }
            __vars.c = __vars.pack.getCards().toArray()[__vars.for___39];
            /** @var Card __vars.c */
            if(!__vars.c.getDeleted()) {
                __vars.cardCount += 1;
            }
        }

        print('' + "\n"
            + '' + "\n"
            + '<div>' + "\n"
            + '    <label>'); print (count(__vars.groups)); print(' groups / '); print (__vars.groupUsers + count(__vars.diffUsers)); print(' users / '); print (__vars.cardCount); print(' cards</label>' + "\n"
            + '    ');
        for (__vars.for___40 in __vars.groups) {
            if(!__vars.groups.hasOwnProperty(__vars.for___40)) { continue; }
            __vars.p = __vars.groups[__vars.for___40];
            /** @var Group __vars.p */
            if (__vars.p.getUsers().count() == 0) {
                continue;
            }
            print('' + "\n"
                + '        <a href="'); print (__vars.view['router'].generate('groups_edit', {'group' : __vars.p.getId()})); print('" class="pack-list">'); print (__vars.p.getName()); print('' + "\n"
                + '            <span>'); print (__vars.p.getUsers().count()); print('</span></a>' + "\n"
                + '    '); }
        for (__vars.for___41 in __vars.diffUsers) {
            if(!__vars.diffUsers.hasOwnProperty(__vars.for___41)) { continue; }
            __vars.g = __vars.diffUsers[__vars.for___41];
            /** @var User __vars.g */
            print('' + "\n"
                + '        <a href="'); print (__vars.view['router'].generate('home_user', {'user' : __vars.g.getId()})); print('" class="pack-list">'); print (implode('', [__vars.g.getFirst() , ' ' , __vars.g.getLast()])); print('' + "\n"
                + '            <span>1</span></a>' + "\n"
                + '    '); } print('' + "\n"
            + '' + "\n"
            + '</div>');});



//-----------------------------------------------------------cell_actions_card-----------------------------------------------------------

    window.views['cell_actions_card'] = ( function cell_actions_card (__vars) {print('<div class="highlighted-link">' + "\n"
        + '    <a title="Remove card" href="#remove-confirm-card">&nbsp;</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------footer-----------------------------------------------------------

    window.views['footer'] = ( function footer (__vars) {print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
        + '    <a href="#add-'); print (__vars.table); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '    <a href="#edit-'); print (__vars.table); print('" class="btn">Edit '); print (ucfirst(str_replace('ss_', '', __vars.table))); print('</a>' + "\n"
        + '    <a href="#cancel-edit" class="btn">Close</a>' + "\n"
        + '    <a href="#save-'); print (__vars.table); print('" class="more">Save</a>' + "\n"
        + '</div>');});



//-----------------------------------------------------------cell_expandMembers_pack-----------------------------------------------------------

    window.views['cell_expandMembers_pack'] = ( function cell_expandMembers_pack (__vars) {print('');






        /** @var Pack __vars.pack */

        __vars.entityIds = [];
        /** @var User[] __vars.users */
        __vars.users = [];
        for (__vars.for___42 in __vars.pack.getUserPacks().toArray()) {
            if(!__vars.pack.getUserPacks().toArray().hasOwnProperty(__vars.for___42)) { continue; }
            __vars.up = __vars.pack.getUserPacks().toArray()[__vars.for___42];
            /** @var UserPack __vars.up */
            __vars.users[count(__vars.users)] = __vars.up.getUser();
        }
        if(isset(__vars.request['ss_group-id']) && !empty(__vars.group = __vars.request['ss_group-id'])) {
            __vars.groupUsers = [];
            for (__vars.for___43 in __vars.users) {
                if(!__vars.users.hasOwnProperty(__vars.for___43)) { continue; }
                __vars.u = __vars.users[__vars.for___43];
                __vars.include = false;
                for (__vars.for___44 in __vars.u.getGroups().toArray()) {
                    if(!__vars.u.getGroups().toArray().hasOwnProperty(__vars.for___44)) { continue; }
                    __vars.g = __vars.u.getGroups().toArray()[__vars.for___44];
                    /** @var Group __vars.g */
                    if(__vars.g.getId() == __vars.group) {
                        __vars.include = true;
                        break;
                    }
                }
                if(__vars.include) {
                    __vars.groupUsers[count(__vars.groupUsers)] = __vars.u;
                }
            }
            __vars.users = __vars.groupUsers;
        }
        else if (isset(__vars.results['ss_group'])) {
            // displaying the list of users not in subgroups which are displayed right below this row
            __vars.groupUsers = [];
            for (__vars.for___45 in __vars.users) {
                if(!__vars.users.hasOwnProperty(__vars.for___45)) { continue; }
                __vars.u = __vars.users[__vars.for___45];
                __vars.include = false;
                for (__vars.for___46 in __vars.u.getGroups().toArray()) {
                    if(!__vars.u.getGroups().toArray().hasOwnProperty(__vars.for___46)) { continue; }
                    __vars.g = __vars.u.getGroups().toArray()[__vars.for___46];
                    /** @var Group __vars.g */
                    if(in_array(__vars.g, __vars.results['ss_group'])) {
                        __vars.include = true;
                        break;
                    }
                }
                if(__vars.include) {
                    __vars.groupUsers[count(__vars.groupUsers)] = __vars.u;
                }
            }
            __vars.users = __vars.groupUsers;
        }
        AdminController.sortByFields(__vars.users, ['first', 'last']);
        __vars.removed = [];
        for (__vars.for___47 in __vars.users) {
            if(!__vars.users.hasOwnProperty(__vars.for___47)) { continue; }
            __vars.u = __vars.users[__vars.for___47];
            if(!empty(__vars.up = __vars.u.getUserPack(__vars.pack)) && __vars.up.getRemoved()) {
                __vars.removed[count(__vars.removed)] = __vars.u;
            }
        }

        __vars.ids = [];
        for (__vars.for___48 in __vars.users) {
            if(!__vars.users.hasOwnProperty(__vars.for___48)) { continue; }
            __vars.u = __vars.users[__vars.for___48];
            __vars.ids[count(__vars.ids)] = implode('', ['ss_user-' , __vars.u.getId()]);
        }

        print('' + "\n"
            + '<form action="'); print (!empty(__vars.group)
            ? __vars.view['router'].generate('save_group', {'ss_group' : {'id' : __vars.group}})
            : __vars.view['router'].generate('packs_create', {'pack' : {'id' : __vars.pack.getId()}})); print('">' + "\n"
            + '    '); print (__vars.view.render('AdminBundle:Admin:cell-collection.html.php', {
            'tables' : {'ss_user' : AdminController.__vars.defaultMiniTables['ss_user']},
            'entities' : __vars.users,
            'entityIds' : __vars.ids,
            'removedEntities' : __vars.removed})); print('' + "\n"
            + '</form>');
    });



//-----------------------------------------------------------heading-----------------------------------------------------------

    window.views['heading'] = ( function heading (__vars) {print(''); print (ucfirst(__vars.field));
    });



//-----------------------------------------------------------heading_id-----------------------------------------------------------

    window.views['heading_id'] = ( function heading_id (__vars) {print('<label class="input">' + "\n"
        + '    <input type="text" name="id" value="" placeholder="All Activity"/>' + "\n"
        + '</label>');});



//-----------------------------------------------------------cell_idTiles_ss_group-----------------------------------------------------------

    window.views['cell_idTiles_ss_group'] = ( function cell_idTiles_ss_group (__vars) {print('<a href="'); print (__vars.view['router'].generate('groups_edit', {'group' : __vars.ss_group.getId()})); print('" class="pack-icon">' + "\n"
        + '    '); print (__vars.view.render('AdminBundle:Admin:cell-id-ss_group.html.php', {'ss_group' : __vars.ss_group})); print('' + "\n"
        + '    '); print (__vars.view.render('AdminBundle:Admin:cell-title.html.php', {'entity' : __vars.ss_group, 'fields' : ['name']})); print('' + "\n"
        + '</a>');
    });



//-----------------------------------------------------------dialog-----------------------------------------------------------

    window.views['dialog'] = ( function dialog (__vars) {print('<div class="modal" id="'); print (__vars.id); print('" tabindex="-1" role="dialog" aria-hidden="true" '); print (isset(__vars.attributes) ? __vars.attributes : ''); print('>' + "\n"
        + '    <div class="modal-dialog">' + "\n"
        + '        <div class="modal-content">' + "\n"
        + '            '); if(__vars.view['slots'].get('modal-header') != null) { print('' + "\n"
        + '            <div class="modal-header">' + "\n"
        + '                <a href="#close" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></a>' + "\n"
        + '                '); __vars.view['slots'].output('modal-header'); print('' + "\n"
        + '            </div>' + "\n"
        + '            '); } print('' + "\n"
        + '            '); if(__vars.view['slots'].get('modal-body') != null) { print('' + "\n"
        + '            <div class="modal-body">' + "\n"
        + '                '); __vars.view['slots'].output('modal-body'); print('' + "\n"
        + '            </div>' + "\n"
        + '            '); } print('' + "\n"
        + '            '); if(__vars.view['slots'].get('modal-footer') != null) { print('' + "\n"
        + '            <div class="modal-footer">' + "\n"
        + '                '); __vars.view['slots'].output('modal-footer'); print('' + "\n"
        + '            </div>' + "\n"
        + '            '); } print('' + "\n"
        + '        </div>' + "\n"
        + '    </div>' + "\n"
        + '</div>' + "\n"
        + '');
        __vars.view['slots'].start('modal-header');
        __vars.view['slots'].stop();
        __vars.view['slots'].start('modal-body');
        __vars.view['slots'].stop();
        __vars.view['slots'].start('modal-footer');
        __vars.view['slots'].stop();
    });



//-----------------------------------------------------------cell_parentOptions_ss_group-----------------------------------------------------------

    window.views['cell_parentOptions_ss_group'] = ( function cell_parentOptions_ss_group (__vars) {print('');

        /** @var Group __vars.ss_group */


        for (__vars.for___49 in __vars.groups) {
            if(!__vars.groups.hasOwnProperty(__vars.for___49)) { continue; }
            __vars.g = __vars.groups[__vars.for___49];
            /** @var Group __vars.g */
            print('' + "\n"
                + '    <option' + "\n"
                + '        value="'); print (__vars.g.getId()); print('" '); print (__vars.g == __vars.ss_group.getParent() ? 'selected="selected"' : ''); print('>'); print (__vars.view.escape(__vars.g.getName())); print('</option>' + "\n"
                + '    ');
            __vars.subGroups = __vars.g.getSubgroups().toArray();
            if (__vars.subGroups > 0) { print('' + "\n"
                + '        <optgroup label="'); print (__vars.view.escape(__vars.g.getName())); print(' Group">' + "\n"
                + '            '); print (__vars.view.render('AdminBundle:Admin:cell-parentOptions-ss_group', {'groups' : __vars.subGroups})); print('' + "\n"
                + '        </optgroup>' + "\n"
                + '        ');
            }
        }
    });



//-----------------------------------------------------------cell_name_pack-----------------------------------------------------------

    window.views['cell_name_pack'] = ( function cell_name_pack (__vars) {print('<label class="input"><input type="text" name="title" placeholder="Give your pack a title" value="'); print (__vars.view.escape(__vars.pack.getTitle())); print('" /></label>');
    });



//-----------------------------------------------------------cell_idTiles_pack-----------------------------------------------------------

    window.views['cell_idTiles_pack'] = ( function cell_idTiles_pack (__vars) {print('<a href="'); print (__vars.view['router'].generate('packs_edit', {'pack' : __vars.pack.getId()})); print('" class="pack-icon">' + "\n"
        + '    '); print (__vars.view.render('AdminBundle:Admin:cell-id-pack.html.php', {'pack' : __vars.pack})); print('' + "\n"
        + '    '); print (__vars.view.render('AdminBundle:Admin:cell-title.html.php', {'entity' : __vars.pack, 'fields' : ['title']})); print('' + "\n"
        + '</a>');
    });



//-----------------------------------------------------------cell_retention_pack-----------------------------------------------------------

    window.views['cell_retention_pack'] = ( function cell_retention_pack (__vars) {print('');




        /** @var Pack __vars.pack */

        if(!empty(__vars.pack.getUser()) && __vars.pack.getUser().getId() == __vars.request['ss_user-id']) {
            __vars.user = __vars.pack.getUser();
        }
        else {
            __vars.user = __vars.pack.getUserById(__vars.request['ss_user-id']);
        }
        __vars.retentionCount = 0;
        if(!empty(__vars.user)) {
            __vars.retention = PacksController.getRetention(__vars.pack, __vars.user);
            for (__vars.for___50 in __vars.retention) {
                if(!__vars.retention.hasOwnProperty(__vars.for___50)) { continue; }
                __vars.r = __vars.retention[__vars.for___50];
                if(__vars.r[2]) {
                    __vars.retentionCount += 1;
                }
            }
        }
        print('' + "\n"
            + '<label>'); print (__vars.retentionCount); print('</label>');});



//-----------------------------------------------------------cell_actions_ss_user-----------------------------------------------------------

    window.views['cell_actions_ss_user'] = ( function cell_actions_ss_user (__vars) {print('');


        /** @var User __vars.ss_user */
        print('' + "\n"
            + '' + "\n"
            + '<div class="highlighted-link">' + "\n"
            + '    <a href="#cancel-edit">Cancel</a>' + "\n"
            + '    <button type="submit" class="more" value="#save-user">Save</button>' + "\n"
            + '    <a title="Send email"' + "\n"
            + '       href="'); print (__vars.view['router'].generate('emails')); print('#'); print (__vars.ss_user.getEmail()); print('"></a>' + "\n"
            + '    <a title="Masquerade"' + "\n"
            + '       href="'); print (__vars.view['router'].generate('_welcome')); print('?_switch_user='); print (__vars.ss_user.getEmail()); print('"></a>' + "\n"
            + '    <a title="Reset password" href="#confirm-password-reset"></a>' + "\n"
            + '    <a title="Edit" href="#edit-user"></a>' + "\n"
            + '    <a title="Remove user" href="#remove-user"></a>' + "\n"
            + '    <a href="#remove-confirm-user" class="more">Remove</a>' + "\n"
            + '</div>');});



//-----------------------------------------------------------cell_properties_pack-----------------------------------------------------------

    window.views['cell_properties_pack'] = ( function cell_properties_pack (__vars) {print('');


        /** @var Pack __vars.pack */
        print('' + "\n"
            + '<div>' + "\n"
            + '    <label class="input">' + "\n"
            + '        <select name="properties[keyboard]">' + "\n"
            + '            <option value="basic">Normal (default)</option>' + "\n"
            + '            <option value="number" '); print (__vars.pack.getProperty('keyboard') == 'number' ? 'selected="selected"' : ''); print('>Numbers only</option>' + "\n"
            + '        </select>' + "\n"
            + '    </label>' + "\n"
            + '</div>');});



//-----------------------------------------------------------cell_collectionRow-----------------------------------------------------------

    window.views['cell_collectionRow'] = ( function cell_collectionRow (__vars) {print('');



// in javascript convert this to window.views.__vars global __vars.i;
        AdminController.__vars.radioCounter++;

        __vars.tableNames = array_keys(__vars.tables);

        __vars.view['slots'].start('cell-collection-row'); print('' + "\n"
            + '    <label class="checkbox buttons-1">' + "\n"
            + '        <input type="checkbox" name="" value="" />' + "\n"
            + '        <input type="hidden" name="" />' + "\n"
            + '        <i></i>' + "\n"
            + '        <span class="entity-title"></span>' + "\n"
            + '        <a href="#insert-entity" title="Add">&nbsp;</a>' + "\n"
            + '        <a href="#subtract-entity" title="Remove"></a>' + "\n"
            + '    </label>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.context = jQuery(__vars.this);
        __vars.newRow = jQuery(__vars.view['slots'].get('cell-collection-row'));
// update names of fields
        __vars.newRow.find('span').text(implode('', [__vars.entity[__vars.tables[__vars.entity['table']][0]], ' ', __vars.entity[__vars.tables[__vars.entity['table']][1]]]));
        __vars.newRow.find('input[type="checkbox"]').attr('name', implode('', [implode('_', __vars.tableNames) , '[' , AdminController.__vars.radioCounter , '][id]'])).val(__vars.entity['id']);
        __vars.newRow.find('input[type="hidden"]').attr('name', implode('', [implode('_', __vars.tableNames) , '[' , AdminController.__vars.radioCounter , '][remove]']));
        if(isset(__vars.entity['removed']) && __vars.entity['removed']) {
            __vars.newRow.find('[href="#subtract-entity"]').remove();
        }
        else {
            __vars.newRow.find('[href="#insert-entity"]').remove();
        }
        __vars.context.append(__vars.newRow);
        print (__vars.context.html());});



//-----------------------------------------------------------packs-----------------------------------------------------------

    window.views['packs'] = ( function packs (__vars) {print('');











        /** @var GlobalVariables __vars.app */
        /** @var __vars.view TimedPhpEngine */
        /** @var __vars.user User */
        /** @var Pack __vars.entity */

        __vars.view.extend('StudySauceBundle:Shared:dashboard.html.php');

        __vars.view['slots'].start('stylesheets');
        for (__vars.for___51 in __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'}).hasOwnProperty(__vars.for___51)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@results_css'], [], {'output' : 'bundles/admin/css/*.css'})[__vars.for___51]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        for (__vars.for___52 in __vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], {'output' : 'bundles/studysauce/css/*.css'})) {
            if(!__vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], {'output' : 'bundles/studysauce/css/*.css'}).hasOwnProperty(__vars.for___52)) { continue; }
            __vars.url = __vars.view['assetic'].stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], {'output' : 'bundles/studysauce/css/*.css'})[__vars.for___52]; print('' + "\n"
                + '    <link type="text/css" rel="stylesheet" href="'); print (__vars.view.escape(__vars.url)); print('"/>' + "\n"
                + ''); }
        __vars.view['slots'].stop();

        __vars.view['slots'].start('javascripts');
        for (__vars.for___53 in __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'}).hasOwnProperty(__vars.for___53)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@AdminBundle/Resources/public/js/results.js'], [], {'output' : 'bundles/admin/js/*.js'})[__vars.for___53]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); }
        for (__vars.for___54 in __vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], {'output' : 'bundles/studysauce/js/*.js'})) {
            if(!__vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], {'output' : 'bundles/studysauce/js/*.js'}).hasOwnProperty(__vars.for___54)) { continue; }
            __vars.url = __vars.view['assetic'].javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], {'output' : 'bundles/studysauce/js/*.js'})[__vars.for___54]; print('' + "\n"
                + '    <script type="text/javascript" src="'); print (__vars.view.escape(__vars.url)); print('"></script>' + "\n"
                + ''); } print('' + "\n"
            + '');
        __vars.view['slots'].stop();

        __vars.view['slots'].start('body'); print('' + "\n"
            + '    <div class="panel-pane" id="packs'); print (__vars.entity !== null ? ('-pack' . intval(__vars.entity.getId())) : ''); print('">' + "\n"
            + '        <div class="pane-content">' + "\n"
            + '            '); if (__vars.entity !== null) { print('' + "\n"
            + '                <form action="'); print (__vars.view['router'].generate('packs_create')); print('" class="pack-edit">' + "\n"
            + '                    ');
            __vars.tables = {};
            __vars.tables['pack'] = {'idEdit' : ['modified', 'created', 'id', 'logo'], 'name' : ['title','userCountStr','cardCountStr'], '1' : 'status', '2' : ['group','groups', 'user','userPacks.user'], '3' : 'properties', '4' : 'actions'};
            __vars.request = {
                // view settings
                'tables' : __vars.tables,
                'headers' : {'pack' : 'packPacks'},
                'footers' : {'pack' : 'packPacks'},
                'new' : empty(__vars.entity.getId()),
                'edit' : empty(__vars.entity.getId()),
                // search settings
                'pack-id' : __vars.entity.getId(),
                'pack-status' : __vars.entity.getDeleted() ? 'DELETED' : '!DELETED',
                // for new=true the template generates the -count number of empty rows, and no database query is performed
                'count-pack' : 1,
            };
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', __vars.request)));
            print('' + "\n"
                + '                </form>' + "\n"
                + '                <div class="group-list">' + "\n"
                + '                    ');
            __vars.tables = {};
            __vars.tables['pack'] = {'0' : 'id', '1' : 'title', 'expandMembers' : [], '2' : ['status'] /* search field but don't display a template */};
            __vars.tables['ss_group'] = {'0' : 'id', '1' : 'title', 'expandMembers' : ['packs', 'groupPacks'], 'actions' : ['deleted'] /* search field but don't display a template */};
            __vars.request = {
                // view settings
                'tables' : __vars.tables,
                'classes' : ['last-right-expand'],
                'headers' : {'pack' : 'createSubGroups'},
                'footers' : false,
                'edit' : false,
                'read-only' : false,
                // search settings
                'pack-id' : empty(__vars.entity.getId()) ? '0' : __vars.entity.getId(),
                'pack-status' : __vars.entity.getDeleted() ? 'DELETED' : '!DELETED',
                'ss_group-deleted' : false,
                'count-ss_group' : 0,
                'count-pack' : 1,
            };
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', __vars.tables)));
            print('' + "\n"
                + '                    <div class="empty-members">' + "\n"
                + '                        <div>Select name on the left to see group members</div>' + "\n"
                + '                    </div>' + "\n"
                + '                </div>' + "\n"
                + '                <div class="card-list">' + "\n"
                + '                    ');
            __vars.newCards = true;
            for (__vars.for___55 in __vars.entity.getCards().toArray()) {
                if(!__vars.entity.getCards().toArray().hasOwnProperty(__vars.for___55)) { continue; }
                __vars.c = __vars.entity.getCards().toArray()[__vars.for___55];
                /** @var Card __vars.c */
                if(!__vars.c.getDeleted()) {
                    __vars.newCards = false;
                    break;
                }
            }
            __vars.tables = {
                // view settings
                'tables' : ['pack', 'card'],
                'expandable' : {'card' : ['preview']},
                'headers' : {'card' : 'packCards'},
                'footers' : {'card' : 'packCards'},
                'new' : __vars.newCards,
                'edit' : empty(__vars.entity.getId()),
                // search settings
                'pack-id' : __vars.entity.getId(),
                'pack-status' : __vars.entity.getDeleted() ? 'DELETED' : '!DELETED',
                // for new=true the template generates the -count number of empty rows, and no database query is performed
                'count-pack' : -1,
                'count-card' : __vars.newCards ? 5 : 0,
            };
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', __vars.tables)));
            print('' + "\n"
                + '                </div>' + "\n"
                + '                ');
        }
        else {
            __vars.tables['count-pack'] = 0;
            __vars.tables['tables'] = {'pack' : {'idTiles' : ['created', 'id', 'title', 'userCountStr', 'cardCountStr'], 'packList' : ['groups', 'userPacks.user'], 'actions' : ['status']}};
            __vars.tables['classes'] = ['tiles'];
            __vars.tables['headers'] = {'pack' : 'newPack'};
            __vars.tables['footers'] = {'pack' : 'newPack'};
            print (__vars.view['actions'].render(new ControllerReference('AdminBundle:Admin:results', __vars.tables)));
        } print('' + "\n"
            + '        </div>' + "\n"
            + '    </div>' + "\n"
            + ''); __vars.view['slots'].stop(); print('' + "\n"
            + '' + "\n"
            + ''); __vars.view['slots'].start('sincludes');
        __vars.view['slots'].stop();

    });



//-----------------------------------------------------------header_groupPacks-----------------------------------------------------------

    window.views['header_groupPacks'] = ( function header_groupPacks (__vars) {print('');




        __vars.entityIds = [];
        for (__vars.for___56 in __vars.results['pack']) {
            if(!__vars.results['pack'].hasOwnProperty(__vars.for___56)) { continue; }
            __vars.p = __vars.results['pack'][__vars.for___56];
            /** @var Pack __vars.p */
            __vars.entityIds[count(__vars.entityIds)] = implode('', ['pack-' , __vars.p.getId()]);
        }

        print('' + "\n"
            + '<header class="'); print (__vars.table); print('">' + "\n"
            + '    <label>Study pack</label>' + "\n"
            + '    <label>Members</label>' + "\n"
            + '    <label>Cards</label>' + "\n"
            + '    <a href="#create-entity" data-target="#create-entity" data-toggle="modal"' + "\n"
            + '       name="ss_group[groupPacks]"' + "\n"
            + '       data-tables="'); print (__vars.view.escape(json_encode({'pack' : AdminController.__vars.defaultMiniTables['pack']}))); print('"' + "\n"
            + '       data-entities="'); print (__vars.view.escape(json_encode(__vars.entityIds))); print('"' + "\n"
            + '       data-action="'); print (__vars.view['router'].generate('save_group', {
            'ss_group' : {'id' : __vars.request['ss_group-id']},
            'tables' : {'ss_group' : ['groupPacks']}
        })); print('" class="big-add">Add' + "\n"
            + '        <span>+</span> new pack</a>' + "\n"
            + '</header>');
    });



//-----------------------------------------------------------cell_actionsGroup_pack-----------------------------------------------------------

    window.views['cell_actionsGroup_pack'] = ( function cell_actionsGroup_pack (__vars) {print('');


        /** @var Pack __vars.pack */

        print('' + "\n"
            + '<div class="highlighted-link">' + "\n"
            + '    <a title="Remove pack/group membership"' + "\n"
            + '       data-confirm="Are you sure you would like to remove the pack &ldquo;'); print (__vars.pack.getTitle()); print('&rdquo; from this group?"' + "\n"
            + '       class="remove-icon" href="#general-dialog"' + "\n"
            + '       data-action="'); print (__vars.view['router'].generate('save_group', {'ss_group' : {'id' : __vars.request['ss_group-id'], 'groupPacks' : {'id' : __vars.pack.getId(), 'remove' : 'true'}}, 'tables' : {'ss_group' : ['groupPacks']}})); print('"' + "\n"
            + '       data-target="#general-dialog" data-toggle="modal">&nbsp;</a>' + "\n"
            + '</div>');});



//-----------------------------------------------------------cell_id_card-----------------------------------------------------------

    window.views['cell_id_card'] = ( function cell_id_card (__vars) {print('');


        /** @var Card __vars.card */

        __vars.isContains = !empty(__vars.card.getCorrect()) && strlen(__vars.card.getCorrect().getValue()) > strlen(trim(__vars.card.getCorrect().getValue(), '__vars.^'));
        __vars.content = __vars.card.getContent();
        __vars.content = preg_replace('/\\\\n(\\\\r)?/i', "\n", __vars.content);
        if ((__vars.hasUrl = preg_match('/https:\/\/.*/i', __vars.content, __vars.matches)) > 0) {
            __vars.url = trim(__vars.matches[0]);
            __vars.isImage = substr(__vars.url, -4) == '.jpg' || substr(__vars.url, -4) == '.jpeg' || substr(__vars.url, -4) == '.gif' || substr(__vars.url, -4) == '.png';
            __vars.isAudio = substr(__vars.url, -4) == '.mp3' || substr(__vars.url, -4) == '.m4a';

        }
        print('' + "\n"
            + '' + "\n"
            + '<label class="input type">' + "\n"
            + '    <span>'); print (__vars.card.getIndex() + 1); print('</span>' + "\n"
            + '    <select name="type">' + "\n"
            + '        <option value="" '); print (empty(__vars.card.getResponseType()) ? 'selected="selected"' : ''); print('>Flash card</option>' + "\n"
            + '        <option value="mc" '); print (__vars.card.getResponseType() == 'mc' ? 'selected="selected"' : ''); print('>Multiple choice</option>' + "\n"
            + '        <option value="tf" '); print (__vars.card.getResponseType() == 'tf' ? 'selected="selected"' : ''); print('>True/False</option>' + "\n"
            + '        <option value="sa contains" '); print (__vars.card.getResponseType() == 'sa' && __vars.isContains ? 'selected="selected"' : ''); print('>Short answer (contains)</option>' + "\n"
            + '        <option value="sa exactly" '); print (__vars.card.getResponseType() == 'sa' && !__vars.isContains ? 'selected="selected"' : ''); print('>Short answer (exact match)</option>' + "\n"
            + '    </select>' + "\n"
            + '</label>' + "\n"
            + '<input name="upload" value="'); print (!empty(__vars.url) ? __vars.url : ''); print('" type="hidden" />' + "\n"
            + '<a href="#upload-image" class="'); print (!empty(__vars.isImage) ? 'active' : ''); print('" data-target="#upload-file" data-toggle="modal"> </a>' + "\n"
            + '<a href="#upload-audio" class="'); print (!empty(__vars.isAudio) ? 'active' : ''); print('" data-target="#upload-file" data-toggle="modal"> </a>' + "\n"
            + '<a href="#upload-video" data-target="#upload-file" data-toggle="modal"> </a>');});



//-----------------------------------------------------------cell_groups-----------------------------------------------------------

    window.views['cell_groups'] = ( function cell_groups (__vars) {print('');




        /** @var User|Group|Pack __vars.entity */
        print('' + "\n"
            + '' + "\n"
            + '<div>' + "\n"
            + '    '); for (__vars.i in __vars.groups) {
            if(!__vars.groups.hasOwnProperty(__vars.i)) { continue; }
            __vars.g = __vars.groups[__vars.i];
            /** @var Group __vars.g */
            print('' + "\n"
                + '        <label class="checkbox">' + "\n"
                + '            <input type="checkbox" name="groups"' + "\n"
                + '                   value="'); print (__vars.g.getId()); print('" '); print (__vars.entity.hasGroup(__vars.g.getName())
                ? 'checked="checked"'
                : ''); print(' /><i></i><span>'); print (__vars.view.escape(__vars.g.getName())); print('</span>' + "\n"
                + '        </label>' + "\n"
                + '        '); if (method_exists(__vars.entity, 'getGroup')) { print('' + "\n"
                + '            <label class="checkbox">' + "\n"
                + '                <input type="checkbox" name="group"' + "\n"
                + '                       value="'); print (__vars.g.getId()); print('" '); print (__vars.entity.getGroup() == __vars.g ? 'checked="checked"' : ''); print(' /><i></i><strong>(owner)</strong>' + "\n"
                + '            </label>' + "\n"
                + '        '); }
        }print('' + "\n"
            + '</div>');});



//-----------------------------------------------------------heading_roles-----------------------------------------------------------

    window.views['heading_roles'] = ( function heading_roles (__vars) {print('<label class="input">' + "\n"
        + '    <select name="roles">' + "\n"
        + '        <option value="">Role</option>' + "\n"
        + '        <option value="_ascending">Ascending (A-Z)</option>' + "\n"
        + '        <option value="_descending">Descending (Z-A)</option>' + "\n"
        + '        <option value="ROLE_PAID">PAID</option>' + "\n"
        + '        <option value="ROLE_ADMIN">ADMIN</option>' + "\n"
        + '        <option value="ROLE_PARENT">PARENT</option>' + "\n"
        + '        <option value="ROLE_PARTNER">PARTNER</option>' + "\n"
        + '        <option value="ROLE_ADVISER">ADVISER</option>' + "\n"
        + '        <option value="ROLE_MASTER_ADVISER">MASTER_ADVISER</option>' + "\n"
        + '        <option value="ROLE_STUDENT">STUDENT</option>' + "\n"
        + '        <option value="ROLE_GUEST">GUEST</option>' + "\n"
        + '        <option value="ROLE_DEMO">DEMO</option>' + "\n"
        + '    </select></label>');});



//-----------------------------------------------------------header_search-----------------------------------------------------------

    window.views['header_search'] = ( function header_search (__vars) {print('<header class="pane-top">' + "\n"
        + '    <div class="search">' + "\n"
        + '        <form action="'); print (__vars.view['router'].generate('command')); print('" method="post">' + "\n"
        + '            <div class="class-names">' + "\n"
        + '                '); for (__vars.table in __vars.tables) {
        if(!__vars.tables.hasOwnProperty(__vars.table)) { continue; }
        __vars.t = __vars.tables[__vars.table]; print('' + "\n"
            + '                    <label class="checkbox">' + "\n"
            + '                        <input type="checkbox" name="tables" value="'); print (__vars.table); print('" checked="checked"/>' + "\n"
            + '                        <i></i> <a' + "\n"
            + '                            href="#'); print (__vars.table); print('">'); print (ucfirst(str_replace('ss_', '', __vars.table))); print('s</a></label>' + "\n"
            + '                '); } print('' + "\n"
        + '            </div>' + "\n"
        + '            <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>' + "\n"
        + '        </form>' + "\n"
        + '    </div>' + "\n"
        + '' + "\n"
        + '    '); for (__vars.table in __vars.tables) {
        if(!__vars.tables.hasOwnProperty(__vars.table)) { continue; }
        __vars.t = __vars.tables[__vars.table];
        print('' + "\n"
            + '        <div class="'); print (__vars.table); print(' paginate">');
        print (__vars.view.render('AdminBundle:Shared:paginate.html.php', {'total' : __vars.results[implode('', [__vars.table , '_total'])]}));
        print('</div>');
    } print('' + "\n"
        + '' + "\n"
        + '    ');
        __vars.templates = []; // template name => classes
        // TODO: build backwards so its right aligned when there are different field counts
        for (__vars.table in __vars.tables) {
            if(!__vars.tables.hasOwnProperty(__vars.table)) { continue; }
            __vars.t = __vars.tables[__vars.table];
            for (__vars.i = 0; __vars.i < count(__vars.t); __vars.i++) {
                __vars.field = is_array(array_values(__vars.t)[__vars.i]) ? array_keys(__vars.t)[__vars.i] : array_values(__vars.t)[__vars.i];
                // skip search only fields
                if(is_numeric(__vars.field)) {
                    continue;
                }
                if (__vars.view.exists(implode('', ['AdminBundle:Admin:heading-' , __vars.field , '-' , __vars.table , '.html.php']))) {
                    __vars.viewName = implode('', ['AdminBundle:Admin:heading-' , __vars.field , '-' , __vars.table , '.html.php']);
                } else {
                    __vars.viewName = implode('', ['AdminBundle:Admin:heading-' , __vars.field , '.html.php']);
                }
                if (isset(__vars.templates[__vars.viewName])) {
                    __vars.templates[__vars.viewName][count(__vars.templates[__vars.viewName])] = __vars.table;
                } else {
                    __vars.templates[__vars.viewName] = [__vars.table];
                }
            }
        }

        for (__vars.table in __vars.tables) {
            if(!__vars.tables.hasOwnProperty(__vars.table)) { continue; }
            __vars.t = __vars.tables[__vars.table]; print('' + "\n"
                + '        <h2 class="'); print (__vars.table); print('">'); print (ucfirst(str_replace('ss_', '', __vars.table))); print('s <a' + "\n"
                + '            href="#add-'); print (__vars.table); print('">+</a>' + "\n"
                + '        <small>('); print (__vars.results[implode('', [__vars.table , '_total'])]); print(')</small></h2>');
        }

        for (__vars.k in __vars.templates) {
            if(!__vars.templates.hasOwnProperty(__vars.k)) { continue; }
            __vars.classes = __vars.templates[__vars.k];
            __vars.field = implode('', [explode('.', explode('-', __vars.k)[1])[0] , ' ' , implode(' ', __vars.classes)]);
            print('' + "\n"
                + '        <div class="'); print (__vars.field); print('">' + "\n"
                + '            ');
            if (__vars.view.exists(__vars.k)) {
                print (__vars.view.render(__vars.k, {'groups' : __vars.allGroups, 'field' : __vars.field}));
            } else {
                print (__vars.view.render('AdminBundle:Admin:heading.html.php', {'groups' : __vars.allGroups, 'field' : __vars.field}));
            }
            print('' + "\n"
                + '        </div>' + "\n"
                + '    '); } print('' + "\n"
            + '    <label class="checkbox"><input type="checkbox" name="select-all"/><i></i></label>' + "\n"
            + '</header>');});



//-----------------------------------------------------------header_packCards-----------------------------------------------------------

    window.views['header_packCards'] = ( function header_packCards (__vars) {print('<header class="card">' + "\n"
        + '    <label>Type</label>' + "\n"
        + '    <label>Prompt</label>' + "\n"
        + '    <label>Answer</label>' + "\n"
        + '</header>');
    });



//-----------------------------------------------------------cell_packList_ss_group-----------------------------------------------------------

    window.views['cell_packList_ss_group'] = ( function cell_packList_ss_group (__vars) {print('');





        /** @var Group __vars.ss_group */
        __vars.entityIds = [];

        __vars.usersGroupsPacks = __vars.ss_group.getUsersPacksGroupsRecursively();
        __vars.users = __vars.usersGroupsPacks[0];
        __vars.packs = __vars.usersGroupsPacks[1];
        __vars.groups = __vars.usersGroupsPacks[2];
        print('' + "\n"
            + '' + "\n"
            + '<div>' + "\n"
            + '    <label>'); print (implode('', [count(__vars.groups) , ' subgroups'])); print(' / '); print (count(__vars.packs)); print(' packs / '); print (count(__vars.users)); print(' users</label>' + "\n"
            + '    ');
        for (__vars.for___63 in __vars.ss_group.getSubgroups().toArray()) {
            if(!__vars.ss_group.getSubgroups().toArray().hasOwnProperty(__vars.for___63)) { continue; }
            __vars.g = __vars.ss_group.getSubgroups().toArray()[__vars.for___63];
            /** @var Group __vars.g */
            if(__vars.g.getDeleted()) {
                continue;
            }

            __vars.subGroupCount = 0;
            for (__vars.for___64 in __vars.g.getSubgroups().toArray()) {
                if(!__vars.g.getSubgroups().toArray().hasOwnProperty(__vars.for___64)) { continue; }
                __vars.c = __vars.g.getSubgroups().toArray()[__vars.for___64];
                /** @var Card __vars.c */
                if(!__vars.c.getDeleted()) {
                    __vars.subGroupCount += 1;
                }
            }

            print('' + "\n"
                + '        <a href="'); print (__vars.view['router'].generate('groups_edit', {'group' : __vars.g.getId()})); print('" class="pack-list">'); print (__vars.view.escape(__vars.g.getName())); print('' + "\n"
                + '            <span>'); print (__vars.subGroupCount); print('</span></a>' + "\n"
                + '    '); } print('' + "\n"
            + '</div>');});



//-----------------------------------------------------------add_entity-----------------------------------------------------------

    window.views['add_entity'] = ( function add_entity (__vars) {print('');



        __vars.view.extend('AdminBundle:Admin:dialog.html.php', {'id' : 'add-entity'});

        __vars.context = jQuery(__vars.this);
        __vars.dialog = __vars.context.find('#add-entity');
        __vars.dialog.find('.tab-pane.active, li.active').removeClass('active');
        __vars.dialog.find('.tab-pane,li').hide();

        __vars.view['slots'].start('modal-header'); print('' + "\n"
            + '<h3>Add or Remove </h3>' + "\n"
            + '<ul class="nav nav-tabs">' + "\n"
            + '    ');
        __vars.first = true;
        for (__vars.tableName in __vars.tables) {
            if(!__vars.tables.hasOwnProperty(__vars.tableName)) { continue; }
            __vars.fields = __vars.tables[__vars.tableName];
            __vars.tabItem = __vars.dialog.find(implode('', ['li a[href="#add-entity-' , __vars.tableName , '"]']));
            if (__vars.tabItem.length == 0) { print('' + "\n"
                + '            <li class="'); print (__vars.first ? 'active' : ''); print('">' + "\n"
                + '                <a href="#add-entity-'); print (__vars.tableName); print('"' + "\n"
                + '                   data-target="#add-entity-'); print (__vars.tableName); print('"' + "\n"
                + '                   data-toggle="tab">'); print (ucfirst(str_replace('ss_', '', (__vars.tableName)))); print('</a></li>' + "\n"
                + '            ');
            }
            else {
                __vars.button = __vars.tabItem.parents('li').show();
                if (__vars.first) {
                    __vars.button.addClass('active');
                }
            }

            __vars.first = false;
        }
        print('</ul>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('modal-body'); print('' + "\n"
            + '<form action="'); print (__vars.view['router'].generate('command_callback')); print('" method="post">' + "\n"
            + '    <div class="tab-content">' + "\n"
            + '        ');
        // remove existing rows
        __vars.dialog.find('.checkbox').remove();
        __vars.first = true;
        for (__vars.tableName in __vars.tables) {
            if(!__vars.tables.hasOwnProperty(__vars.tableName)) { continue; }
            __vars.fields = __vars.tables[__vars.tableName];
            __vars.entityField = __vars.dialog.find(implode('', ['input[name="', __vars.tableName, '"][type="text"]']));
            if(__vars.dialog.find(implode('', ['#add-entity-', __vars.tableName])).length == 0) {
                __vars.tmpTables = {};
                __vars.tmpTables[__vars.tableName] = __vars.tables[__vars.tableName];
                print('' + "\n"
                    + '                <div id="add-entity-'); print (__vars.tableName); print('"' + "\n"
                    + '                     class="tab-pane '); print (__vars.first ? 'active' : ''); print('">' + "\n"
                    + '                    '); print (__vars.view.render('AdminBundle:Admin:cell-collection.html.php', {
                    'context' : __vars.entityField.length > 0
                        ? __vars.entityField.parents('.tab-pane')
                        : jQuery('<div/>'), 'tables' : __vars.tmpTables,
                    'entities' : __vars.entities,
                    'entityIds' : __vars.entityIds,
                    'inline' : true})); print('' + "\n"
                    + '                </div>' + "\n"
                    + '                ');
            }

            __vars.entityField.parents('.tab-pane').show();

            if (__vars.first) {
                __vars.entityField.parents('.tab-pane').addClass('active');
            }
            __vars.first = false;
        }
        print('' + "\n"
            + '    </div>' + "\n"
            + '</form>' + "\n"
            + ''); __vars.view['slots'].stop();

        __vars.view['slots'].start('modal-footer'); print('' + "\n"
            + '<a href="#close" class="btn" data-dismiss="modal">Cancel</a>' + "\n"
            + '<a href="#submit-entities" class="btn btn-primary" data-dismiss="modal">Save</a>' + "\n"
            + ''); __vars.view['slots'].stop();

// TODO: decide what to we do when it is extended?  what does jQuery(__vars.this) do?
        if(__vars.dialog.length > 0) {
            //__vars.dialog = __vars.context.append(__vars.view['slots'].get('cell_status_pack')).find('form');
            __vars.dialog.find('.nav-tabs').append(jQuery(__vars.view['slots'].get('modal-header')).find('li'));
            __vars.dialog.find('.tab-content').append(jQuery(__vars.view['slots'].get('modal-body')).find('.tab-pane'));
        }


    });



//-----------------------------------------------------------footer_newGroup-----------------------------------------------------------

    window.views['footer_newGroup'] = ( function footer_newGroup (__vars) {print('<div class="highlighted-link form-actions '); print (__vars.table); print('">' + "\n"
        + '    <a href="'); print (__vars.view['router'].generate('groups_new')); print('" class="big-add">Add' + "\n"
        + '        <span>+</span> new '); print (str_replace('ss_', '', __vars.table)); print('</a>' + "\n"
        + '    <span><br />&nbsp;<br /></span>' + "\n"
        + '</div>');});



//-----------------------------------------------------------footer_groupCount-----------------------------------------------------------

    window.views['footer_groupCount'] = ( function footer_groupCount (__vars) {print('');
        if (empty(__vars.results['ss_group-1'])) { print('' + "\n"
            + '    <footer class="ss_group">' + "\n"
            + '        <label>Total in this Group</label>' + "\n"
            + '        <label>0</label>' + "\n"
            + '        <label>0</label>' + "\n"
            + '    </footer>' + "\n"
            + ''); } else {
            __vars.userPacksGroups = __vars.results['ss_group-1'][0].getUsersPacksGroupsRecursively();
            __vars.users = __vars.userPacksGroups[0];
            __vars.packs = __vars.userPacksGroups[1];
            __vars.groups = __vars.userPacksGroups[2];
            print('' + "\n"
                + '    <footer class="ss_group">' + "\n"
                + '        <label>Total in this Group</label>' + "\n"
                + '        <label>'); print (count(__vars.users)); print('</label>' + "\n"
                + '        <label>'); print (count(__vars.packs)); print('</label>' + "\n"
                + '    </footer>' + "\n"
                + '    ');
        }
    });



})(jQuery);