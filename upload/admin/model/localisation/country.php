<?php
namespace Opencart\Admin\Model\Localisation;
class Country extends \Opencart\System\Engine\Model {
	public function addCountry($data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "country` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `iso_code_2` = '" . $this->db->escape((string)$data['iso_code_2']) . "', `iso_code_3` = '" . $this->db->escape((string)$data['iso_code_3']) . "', `address_format` = '" . $this->db->escape((string)$data['address_format']) . "', `postcode_required` = '" . (int)$data['postcode_required'] . "', `status` = '" . (int)$data['status'] . "'");

		$this->cache->delete('country');

		return $this->db->getLastId();
	}

	public function editCountry($country_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "country` SET `name` = '" . $this->db->escape((string)$data['name']) . "', `iso_code_2` = '" . $this->db->escape((string)$data['iso_code_2']) . "', `iso_code_3` = '" . $this->db->escape((string)$data['iso_code_3']) . "', `address_format` = '" . $this->db->escape((string)$data['address_format']) . "', `postcode_required` = '" . (int)$data['postcode_required'] . "', `status` = '" . (int)$data['status'] . "' WHERE `country_id` = '" . (int)$country_id . "'");

		$this->cache->delete('country');
	}

	public function deleteCountry($country_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "country` WHERE `country_id` = '" . (int)$country_id . "'");

		$this->cache->delete('country');
	}

	public function getCountry($country_id): array {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "country` WHERE `country_id` = '" . (int)$country_id . "'");

		return $query->row;
	}

	public function getCountries($data = []): array {
		if ($data) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "country`";

			$implode = [];

			if (!empty($data['filter_name'])) {
				$implode[] = "`name` LIKE '" . $this->db->escape((string)$data['filter_name']) . "%'";
			}

			if (!empty($data['filter_iso_code_2'])) {
				$implode[] = "`iso_code_2` LIKE '" . $this->db->escape((string)$data['filter_iso_code_2']) . "%'";
			}

			if (!empty($data['filter_iso_code_3'])) {
				$implode[] = "`iso_code_3` LIKE '" . $this->db->escape((string)$data['filter_iso_code_3']) . "%'";
			}

			if ($implode) {
				$sql .= " WHERE " . implode(" AND ", $implode);
			}

			$sort_data = [
				'name',
				'iso_code_2',
				'iso_code_3'
			];

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY `" . $data['sort'] . "`";
			} else {
				$sql .= " ORDER BY `name`";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$country_data = $this->cache->get('country.admin');

			if (!$country_data) {
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` ORDER BY `name` ASC");

				$country_data = $query->rows;

				$this->cache->set('country.admin', $country_data);
			}

			return $country_data;
		}
	}

	public function getTotalCountries($data = []): int {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "country`";

		$implode = [];

		if (!empty($data['filter_name'])) {
			$implode[] = "`name` LIKE '" . $this->db->escape((string)$data['filter_name']) . "%'";
		}

		if (!empty($data['filter_iso_code_2'])) {
			$implode[] = "`iso_code_2` LIKE '" . $this->db->escape((string)$data['filter_iso_code_2']) . "%'";
		}

		if (!empty($data['filter_iso_code_3'])) {
			$implode[] = "`iso_code_3` LIKE '" . $this->db->escape((string)$data['filter_iso_code_3']) . "%'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
