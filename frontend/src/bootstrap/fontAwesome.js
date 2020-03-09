import { dom, library } from '@fortawesome/fontawesome-svg-core'
// fas icon (solid)
import {
  faCircleNotch,
  faUsers,
  faMapMarkerAlt,
  faDownload,
  faTrash,
  faEdit,
  faPlusCircle,
} from '@fortawesome/free-solid-svg-icons'
// fab icon (brand)
import {
  faFacebookSquare,
} from '@fortawesome/free-brands-svg-icons'
// far icon (regular)
import {
  faClock,
  faMap,
} from '@fortawesome/free-regular-svg-icons'

library.add(
  faFacebookSquare,
  faCircleNotch,
  faClock,
  faUsers,
  faMap,
  faMapMarkerAlt,
  faDownload,
  faTrash,
  faEdit,
  faPlusCircle,
)

// This will kick of the initial replacement of i to svg tags and configure a MutationObserver
dom.watch()
